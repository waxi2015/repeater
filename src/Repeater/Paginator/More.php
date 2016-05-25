<?php

namespace Waxis\Repeater\Repeater\Paginator;

class More extends Ancestor {

	public $template = 'more.phtml';

	public $refresh = false;
	
	public $autoload = false;

	public function __construct ($descriptor, $options) {
		if (isset($options['refresh'])) {
			$this->refresh = $options['refresh'];
		}

		if (isset($options['autoload'])) {
			$this->autoload = $options['autoload'];
		}

		parent::__construct($descriptor, $options);
	}

	public function getAutoload () {
		return $this->autoload;
	}
	
}