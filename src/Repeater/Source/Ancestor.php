<?php

namespace Waxis\Repeater\Repeater\Source;

class Ancestor {

	public $source = null;

	public $filters = null;
	
	public $where = null;

	public $page = null;

	public $limit = null;

	public $fromZero = false;

	public $ahead = null;

	public $refresh = false;

	public $order = 'DESC';

	public $orderBy = null;

	public function __construct ($source, $options) {
		$this->source = $source;

		if (isset($options['order'])) {
			$this->order = $options['order'];
		}

		if (isset($options['orderBy'])) {
			$this->orderBy = $options['orderBy'];
		}
	}

	public function getFilterValue ($filter) {
		$descriptorValue = $this->getFilterDescriptorValue($filter);
		$setValue = $this->getFilterSetValue($filter);

		/*
	
		@todo: under watch
		
		if (empty($descriptorValue) && empty($setValue)) {
			return null;
		} elseif (!empty($setValue)) {
			$value = $setValue;
		} elseif (!empty($descriptorValue)) {
			$value = $descriptorValue;
		}*/

		if (($descriptorValue === null || $descriptorValue === '') && ($setValue === null || $setValue === '')) {
			return null;
		} elseif ($setValue !== null && $setValue !== '') {
			$value = $setValue;
		} elseif ($descriptorValue !== null && $descriptorValue !== '') {
			$value = $descriptorValue;
		}

		return trim($value);
	}

	public function getFilterDescriptorValue ($filter) {
		return isset($filter['value']) && $filter['value'] != '' ? $filter['value'] : null;
	}

	public function getFilterSetValue ($filter) {
		$setValue = isset($this->filterValues[$filter['name']]) && $this->filterValues[$filter['name']] != '' ? $this->filterValues[$filter['name']] : null;

		if (is_array($setValue)) {
			$hasSetValue = false;
			foreach ($setValue as $one) {
				if ($one != '') {
					$hasSetValue = true;
				}
			}
			if (!$hasSetValue) {
				$setValue = null;
			}
		}

		return $setValue;
	}

	public function getFrom () {
		if ($this->fromZero) {
			$from = 0;
		} else {
			$from = ($this->page - 1) * $this->limit;
		}

		if ($this->ahead !== null) {
			$from = $from - $this->ahead;

			if ($from < 0) {
				$from = 0;
			}
		}

		return $from;
	}

	public function getData () { }

	public function getTotalCount () { }

	public function ahead ($ahead) {
		$this->ahead = $ahead;
	}

	public function refresh () {
		$this->refresh = true;
	}

	public function fromZero () {
		$this->fromZero = true;
	}

	public function setPage ($page) {
		$this->page = $page;
	}

	public function getPage () {
		return $this->page;
	}

	public function setLimit ($limit) {
		$this->limit = $limit;
	}

	public function getLimit () {
		$limit = $this->limit;

		if ($this->refresh) {
			$limit = $limit * $this->page;
		}

		return $limit;
	}

	public function getOrder () {
		return $this->order;
	}

	public function getOrderBy () {
		return $this->orderBy;
	}
}