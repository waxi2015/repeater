<?php

namespace Waxis\Repeater\Repeater\Source;

class Db extends Ancestor {

	public function getData () {
		$query = $this->getBaseQuery();

		if ($this->limit !== null) {
			$query->take($this->getLimit());
			$query->skip($this->getFrom());
		}

		if ($this->orderBy !== null) {
			if (is_array($this->orderBy)) {
				$order = array();

				foreach ($this->orderBy as $key => $one) {
					$query->orderBy($one, $this->order[$key]);
				}
			} else {
				$query->orderBy($this->orderBy, $this->order);
			}
		}
		
		return $query->get();
	}

	public function getTotalCount () {
		return $this->getBaseQuery()->count();
	}

	public function getBaseQuery () {
		$query = \DB::table($this->source);

		if ($this->where !== null) {
			$query->whereRaw('('.$this->where.')');
		}

		if ($this->filters !== null) {
			$filter = $this->filter();

			if (!empty($filter)) {
				$query->whereRaw($filter);
			}
		}

		# Laravel was missing reset order/limit/offset
		$query->orders = null;
		$query->offset = null;
		$query->limit = null;

		return $query;
	}

	public function filter () {
		$where = '';
		foreach ($this->filters as $filter) {
			$wherepart = '';

			if (!isset($filter['type'])) {
				$filter['type'] = 'text';
			}

			if (!is_array($filter['fields'])) {
				$filter['fields'] = array($filter['fields']);
			}

			$value = $this->getFilterValue($filter);

			if ($value === null) {
				continue;
			}

			foreach ($filter['fields'] as $field) {

				if (strlen($wherepart) > 0) {
					$wherepart .= ' OR ';
				} else {
					$wherepart .= '';
				}

				switch (strtoupper($filter['type'])) {
					case 'DATE':
						$from = !empty($value[0]) ? $value[0] : '0000-01-01';
						$to = !empty($value[1]) ? $value[1] : '9999-01-01';

						$wherepart .= '`' . $field . '` BETWEEN "' . $from . ' 00:00:00" AND "' . $to . ' 23:59:59"';
						break;

					case 'SELECT':
						$wherepart .= '`' .$field . '` like "' . $value . '"';
						break;

					case 'MULTISELECT':
						$wherepart .= '`' .$field . '` IN ("'.implode('","', $value).'")';
						break;

					default:
						$wherepart .= '`' .$field . '` like "%' . $value . '%"';
						break;
				}

				$wherepart .= '';
			}

			if (strlen($wherepart) > 0) {
				$wherepart = '(' . $wherepart . ')';
			}

			if (strlen($where) > 0) {
				$wherepart = ' AND ' . $wherepart;
			}

			$where .= $wherepart;
		}

		if (empty($where)) {
			return null;
		}
		return $where;
	}
}