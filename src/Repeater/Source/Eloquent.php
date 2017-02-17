<?php

namespace Waxis\Repeater\Repeater\Source;

class Eloquent extends Db {

	public function getData () {
		$query = $this->getBaseQuery();

		if ($this->limit !== null) {
			$query = $query->take($this->getLimit());
			$query = $query->skip($this->getFrom());
		}

		if ($this->orderBy !== null) {
			if (is_array($this->orderBy)) {
				$order = array();

				foreach ($this->orderBy as $key => $one) {
					$query = $query->orderBy($one, $this->order[$key]);
				}
			} else {
				
				$query = $query->orderBy($this->orderBy, $this->order);
			}
		}

		return $query->get();
	}

	public function getBaseQuery () {
		$query = $this->source;

		if ($this->where !== null) {
			if (strstr(strtolower($query->toSql()), strtolower($this->where)) === false) {
				$query = $query->whereRaw($this->where);
			}
		}

		if ($this->filters !== null) {
			$filter = $this->filter();

			if (!empty($filter)) {
				if (strstr(strtolower($query->toSql()), strtolower($filter)) === false) {
					$query = $query->whereRaw($filter);
				}
			}
		}

		# Laravel was missing reset order/limit/offset
		$query->orders = null;
		$query->offset = null;
		$query->limit = null;

		return $query;
	}
}