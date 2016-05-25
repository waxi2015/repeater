<?php

namespace Waxis\Repeater\Repeater\Source;

class Query extends Db {

	public function getBaseQuery () {
		$query = $this->source;

		if ($this->where !== null) {
			$query->whereRaw($this->where);
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
}