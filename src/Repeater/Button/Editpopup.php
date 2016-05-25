<?php

namespace Waxis\Repeater\Repeater\Button;

class Editpopup extends Ancestor {

	public $baseClass = 'wax-repeater-edit-popup btn-sm btn-primary';

	public function __construct ($descriptor, $data = null, $index = null) {
		parent::__construct($descriptor, $data, $index);

		$this->domData = array(
			'id' => $this->getData('id')
		);
	}
}