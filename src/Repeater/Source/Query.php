<?php

namespace Waxis\Repeater\Repeater\Source;

class Query extends Db {

	public function getBaseQuery () {
		$query = $this->source;

		if ($this->where !== null) {
			if (strstr(strtolower($query->toSql()), strtolower($this->where)) === false) {
				$query->whereRaw($this->where);
			}
		}

		if ($this->filters !== null) {
			$filter = $this->filter();

			if (!empty($filter)) {
				if (strstr(strtolower($query->toSql()), strtolower($filter)) === false) {
					$query->whereRaw($filter);
				}
			}
		}
		//DX($this->filters);
		# Laravel was missing reset order/limit/offset
		$query->orders = null;
		$query->offset = null;
		$query->limit = null;

		return $query;
	}
}