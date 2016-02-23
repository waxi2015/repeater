<?php

namespace Waxis\Lister\Lister;

class Config {

	public $templateDirectory = '/Template';


	public function getTemplateDirectory () {
		return  __DIR__ . $this->templateDirectory . '/';
	}
}