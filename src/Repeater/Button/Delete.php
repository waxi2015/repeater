<?php

namespace Wax\Repeater\Repeater\Button;

class Delete extends Ancestor {

	public $baseClass = 'wax-repeater-delete';

	public function __construct ($descriptor, $data = null, $index = null) {
		parent::__construct($descriptor, $data, $index);

		$this->domData = array(
			'id' => $this->getData('id')
		);
	}
}