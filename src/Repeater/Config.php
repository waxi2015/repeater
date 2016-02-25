<?php

namespace Waxis\Repeater\Repeater;

class Config {

	public $templateDirectory = '/Template';


	public function getTemplateDirectory () {
		return  __DIR__ . $this->templateDirectory . '/';
	}
}