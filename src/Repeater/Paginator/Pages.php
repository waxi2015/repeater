<?php

namespace Waxis\Repeater\Repeater\Paginator;

class Pages extends Ancestor {

	public $paginatorType = 'pages';

	public $template = 'pages.phtml';

	public $totalPages = null;

	public function getPages () {
		$totalPages = $this->getTotalPages();

		$pagesBefore = round(($this->displayPages - 1) / 2);
		$pagesAfter = $pagesBefore;

		$firstPage = $this->page - $pagesBefore;

		$addToLastPage = 0;
		if ($firstPage < 1) {
			$addToLastPage = $firstPage * -1 + 1;
			$firstPage = 1;
		}

		$lastPage = $this->page + $pagesAfter + $addToLastPage;

		if ($lastPage > $totalPages) {
			$addToFirstPage = ($totalPages - $lastPage) * -1;
			$lastPage = $totalPages;

			if ($firstPage > 1) {
				$firstPage -= $addToFirstPage;

				if ($firstPage < 1) {
					$firstPage = 1;
				}
			}
		}

		$pages = [];
		for ($b = $firstPage; $b < $this->page; $b++) {
			$pages[] = [
				'page' => $b,
				'current' => false,
				'first' => $b == 1 ? true : false,
				'last' => $b == $totalPages ? true : false,
			];
		}
		if ($this->page <= $totalPages) {
			$pages[] = [
				'page' => $this->page,
				'current' => true,
				'first' => $this->page == 1 ? true : false,
				'last' => $this->page == $totalPages ? true : false,
			];
		}
		for ($a = $this->page + 1; $a <= $lastPage; $a++) {
			$pages[] = [
				'page' => $a,
				'current' => false,
				'first' => $a == 1 ? true : false,
				'last' => $a == $totalPages ? true : false,
			];
		}

		return $pages;
	}
}