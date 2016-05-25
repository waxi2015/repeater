<?php

namespace App\Descriptors\Repeater;

class Example extends \Waxis\Repeater\Repeater\Descriptor {

	public function descriptor()
	{
		return [
			'id' => 'users',
			'table' => 'users',
			'fields' => [
				[
					'label' => 'ID',
					'name' => 'id',
				]
			]
		];
	}
}