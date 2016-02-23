<?php

namespace Waxis\Lister\Lister;

class Ancestor {

	public $type = null;

	public $config = null;

	public $templateDirectory = null;

	public $template = null;
	
	public $descriptor = null;

	public function __construct ($descriptor) {
		if (isset($descriptor['templateDirectory'])) {
			$this->templateDirectory = $descriptor['templateDirectory'];
		}

		if (isset($descriptor['template'])) {
			$this->template = $descriptor['template'];
		}

		if ($this->descriptor === null) {
			$this->descriptor = $descriptor;
		}
	}

	public function getType () {
		if ($this->type === null) {
			throw new Exception('Type must be defined at: ' . get_called_class(),1);
		}

		return $this->type;
	}

	public function getConfig () {
		if (!$this->config) {
			if (file_exists(app_path() . '/Configs/Lister.php')) {
				$config = new \App\Configs\Lister;
			} else {
				$config = new Config;
			}
			$this->config = $config;
		}

		return $this->config;
	}

	// template directory can be a fill server path
	public function getTemplateDirectory () {
		if ($this->templateDirectory === null) {
			$config = $this->getConfig();
			return $config->getTemplateDirectory();
		}

		return $this->templateDirectory;
	}

	public function renderJavascript ($template = false) {
		if (!$template) {
			$template = $this->template;
		}

		$template = 'javascript/' . $template;

		echo $this->fetch($template);
	}

	public function render ($template = false) {
		echo $this->fetch($template);
	}

	public function fetch ($template = false) {

		ob_start();
		include($this->getTemplate($template));
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	# Bit messy but needed
	public function getTemplate ($template = false) {
		if (!$template) {
			$template = $this->template;
		}

		$config = $this->getConfig();

		if ($this->templateDirectory !== null) {
			# 1st - if directory was given manually - check if it exists in the <given>/<type>/ dir
			$pathToTemplate = $this->getTemplateDirectory() . $this->getType() . $template;

			if (!file_exists($pathToTemplate)) {
				# 2nd - if it's not in <given>/<type>/ try <given>/
				$pathToTemplate = $this->getTemplateDirectory() . $template;
			}

			if (!file_exists($pathToTemplate) && $this->templateDirectory !== null) {
				# 3rd - if it's given but not even in the <given> throw error
				throw new \Exception('Template not found: ' . $pathToTemplate,1);
				return false;
			}
		} else {
			# 4th - if it was not given, check out <app>/<type>/ first
			$pathToTemplate = base_path() . '/resources/views/lister/' . $this->getType() . '/' . $template;
			if (!file_exists($pathToTemplate)) {
				# 5th - if it's not in <app>/<type>/ check out <app>/
				$pathToTemplate = base_path() . '/resources/views/lister/' . $template;
			}

			# 6th - if it was not in the app, check out <wx-list>/<type>/ first
			if (!file_exists($pathToTemplate)) {
				$pathToTemplate = $config->getTemplateDirectory() . $this->getType() . '/' . $template;
			}

			if (!file_exists($pathToTemplate)) {
				# 7th - if it's not in <wx-list>/<type>/ check out <wx-list>/
				$pathToTemplate = $config->getTemplateDirectory() . $template;
			}
		}

		# 8th - if it's not any of the places then you missed it
		if (!file_exists($pathToTemplate)) {
			throw new \Exception('Template not found: ' . $pathToTemplate,1);
			return false;
		}

		return $pathToTemplate;
	}
}