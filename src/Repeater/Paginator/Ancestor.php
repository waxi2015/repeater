<?php

namespace Waxis\Repeater\Repeater\Paginator;

class Ancestor extends \Waxis\Repeater\Repeater\Ancestor {

	public $type = 'paginator';

	public $limit = 10; // default

	public $total = null;

	public $displayPages = 5; // default

	public $mode = 'ajax';

	public $page = null;

	public $totalPages = null;

	public $listId = null;

	public $init = true;

	public $paginatorType = null;

	public function __construct ($descriptor, $options) {
		$this->page = $options['page'];
		$this->total = $options['total'];

		if (isset($options['limit'])) {
			$this->limit = $options['limit'];
		}

		if (isset($options['displayPages'])) {
			$this->displayPages = $options['displayPages'];
		}

		if (isset($options['listId'])) {
			$this->listId = $options['listId'];
		}

		if (isset($options['init'])) {
			$this->init = $options['init'];
		}

		if (isset($descriptor['mode'])) {
			$this->mode = $descriptor['mode'];
		}

		parent::__construct($descriptor);
	}

	public function getPageInfo () {
		$from = ($this->page * $this->limit) - $this->limit + 1;
		$to = $from + $this->limit - 1;
		$total = $this->getTotal();

		if ($to > $total) {
			$to = $total;
		}

		return trans('repeater.paginator.display_text', compact('from','to','total'));
	}

	public function getPageBaseUrl () {
		return preg_replace('/\/[0-9]*$/','',\Request::url());
	}

	public function getPageUrl($page)
	{
		return $this->getPageBaseUrl() . '/' . $page;
	}

	public function getPrevPageUrl()
	{
		return $this->getPageBaseUrl() . '/' . $this->getPrevPageNumber();
	}

	public function getNextPageUrl()
	{
		return $this->getPageBaseUrl() . '/' . $this->getNextPageNumber();
	}

	public function getLastPageUrl()
	{
		$page = $this->getTotalPages();

		return $this->getPageBaseUrl() . '/' . $page;
	}

	public function getPrevPageNumber () {
		$page = $this->getPrevPage();

		if (!$page) {
			$page = 1;
		}

		return $page;
	}

	public function getNextPageNumber () {
		$page = $this->getNextPage();

		if (!$page) {
			$page = $this->getTotalPages();
		}

		return $page;
	}

	public function getPage () {
		return $this->page;
	}

	public function getTotalPages () {
		if ($this->totalPages === null) {
			$this->totalPages = ceil($this->total / $this->limit);
		}

		return $this->totalPages;
	}

	public function getNextPage () {
		$next = $this->page + 1;

		if ($next > $this->getTotalPages()) {
			$next = false;
		}

		return $next;
	}

	public function getPrevPage () {
		$prev = $this->page - 1;

		if ($prev <= 0) {
			$prev = false;
		}

		return $prev;
	}

	public function getTotal () {
		return $this->total;
	}

	public function getLimit () {
		return $this->limit;
	}

	public function getDisplayPages () {
		return $this->displayPages;
	}

	public function getListId () {
		return $this->listId;
	}

	public function getDescriptorId () {
		return $this->getListId();
	}

	public function getMode () {
		return strtolower($this->mode);
	}

	public function getInit () {
		return $this->init;
	}

	public function getPaginatorType () {
		return $this->paginatorType;
	}
}