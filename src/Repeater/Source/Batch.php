<?php

namespace Waxis\Repeater\Repeater\Source;

class Batch extends Ancestor {

	public $baseData = null;

	public function getData () {
		$return = $this->getBaseData();		

		if ($this->orderBy !== null) {
			if (is_array($this->orderBy)) {
				$order = array();
				$multisortOptions = [];

				foreach ($return as $k => $v) {
					foreach ($this->orderBy as $orderKey => $orderValue) {
						$order[$orderValue][$k] = $v[$orderValue];
					}
				}

				foreach ($this->orderBy as $key => $value) {
					$multisortOptions[] = $order[$orderValue];
					$multisortOptions[] = $this->order[$key] == 'ASC' ? SORT_ASC : SORT_DESC;
				}

				$multisortOptions[] = $return;

				call_user_func_array('array_multisort', $multisortOptions);
			} else {
				sortBy($this->orderBy, $return, $this->order);
			}
		}

		if ($this->limit !== null) {
			$return = array_slice($return, $this->getFrom(), $this->getLimit());
		}

		return $return;
	}

	public function getBaseData () {
		if ($this->baseData !== null) {
			return $this->baseData;
		}

		$source = $this->source;

		if ($this->filters !== null) {
			$source = $this->getFilteredData($source);
		}

		$this->baseData = $source;

		return $source;
	}

	public function getFilteredData ($source) {
		$temp = array();

		foreach ($source as $key => $row) {
			$match = true;

			foreach ($this->filters as $filter) {
				if (!isset($filter['type'])) {
					$filter['type'] = 'text';
				}
				
				$filterValue = $this->getFilterValue($filter);

				if ($filterValue === null) {
					continue;
				}

				$fields = $filter['fields'];

				if (!is_array($fields)) {
					$fields = array($fields);
				}

				$fieldMatch = false;
				foreach ($fields as $field) {
					$fieldValue = $row[$field];

					switch (strtoupper($filter['type'])) {
						case 'DATE':
							$fieldValue = strtotime($fieldValue);

							$from = strtotime(!empty($filterValue[0]) ? $filterValue[0] : '0000-00-00');
							$to = strtotime(!empty($filterValue[1]) ? $filterValue[1] : '9999-01-01');

							if ($fieldValue >= $from && $fieldValue <= $to) {
								$fieldMatch = true;
							}

							break;

						case 'SELECT':
							if ((string) $fieldValue == (string) $filterValue) {
								$fieldMatch = true;
							}
							break;

						default:
							if (strstr(linkRewrite($fieldValue), linkRewrite($filterValue)) !== false) {
								$fieldMatch = true;
							}
							break;
					}
				}

				if (!$fieldMatch) {
					$match = false;
				}
			}

			if ($match) {
				$temp[$key] = $row;
			}
		}

		return $temp;
	}

	public function getTotalCount () {
		return count($this->getBaseData());
	}
}