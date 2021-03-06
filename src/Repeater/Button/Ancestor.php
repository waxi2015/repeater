<?php

namespace Waxis\Repeater\Repeater\Button;

class Ancestor extends \Waxis\Repeater\Repeater\Ancestor {

	public $type = 'button'; // also directory name

	public $template = 'abstract.phtml';

	public $label = null;

	public $url = null;

	public $mode = 'reload';

	public $options = null;

	public $data = null;

	public $index = null;

	public $table = null;

	public $listId = null;

	public $baseClass = null;

	public $class = null;

	public $domData = null;

	public $permission = null;

	public $roles = null;

	public function __construct ($descriptor, $data = null, $index = null) {
		$this->data = $data;
		$this->index = $index;

		if (isset($descriptor['label'])) {
			$this->label = $descriptor['label'];
		}

		if (isset($descriptor['template'])) {
			$this->template = $descriptor['template'];
		}

		if (isset($descriptor['url'])) {
			$this->url = $descriptor['url'];
		}

		if (isset($descriptor['mode'])) {
			$this->mode = $descriptor['mode'];
		}

		if (isset($descriptor['options'])) {
			$this->options = $descriptor['options'];
		}

		if (isset($descriptor['table'])) {
			$this->table = $descriptor['table'];
		}

		if (isset($descriptor['listId'])) {
			$this->listId = $descriptor['listId'];
		}

		if (isset($descriptor['class'])) {
			$this->class = $descriptor['class'];
		}

		if (isset($descriptor['domData'])) {
			$this->domData = $descriptor['domData'];
		}

		if (isset($descriptor['permission'])) {
			$this->permission = $descriptor['permission'];
		}

		if (isset($descriptor['roles'])) {
			$this->roles = $descriptor['roles'];
		}

		parent::__construct($descriptor);
	}

	public function hasPermission () {
		if ($this->permission === null) {
			return true;
		}

		if (!\Auth::guard($this->permission)->check()) {
			return false;
		}

		$roles = is_array($this->roles) ? $this->roles : [$this->roles];

		if (in_array(\Auth::guard($this->permission)->user()->role, $roles)) {
			return true;
		}

		return false;
	}

	public function getHrefString () {
		return 'href="' . $this->getUrl() . '"';
	}

	public function getDataValue ($key) {
		$row = $this->getRow();

		if (isset($row->$key)) {
			return $row->$key;
		}

		if (array_key_exists($key, $row)) {
			return $row[$key];
		}

		return null;
	}

	public function getRow () {
		return $this->data[$this->index];
	}

	public function getLabel () {
		return trans($this->label);
	}

	public function getUrl () {
		return $this->replaceVars($this->url);
	}

	protected function replaceVars ($str) {
		$replaced = $str;

		# replace data vars to values
		preg_match_all('(%[a-zA-Z0-9]+)', $str, $matches);
		
		foreach ($matches[0] as $one) {
			$var = str_replace('%','',$one);

			# check for class properties first
			$value = property_exists($this, $var) ? $this->$var : false;

			# if it's not a property look for it in the data
			if ($value === false) {
				$value = isset($this->data[$this->index]->{$var}) ? $this->data[$this->index]->{$var} : false;
			}

			# if it's not a property look for it in the data array
			if ($value === false) {
				$value = isset($this->data[$this->index][$var]) ? $this->data[$this->index][$var] : false;
			}
			
			$replaced = str_replace($one, $value, $str);
		}

		return $replaced;
	}

	public function getMode () {
		return $this->mode;
	}

	public function getOptions () {
		return $this->options;
	}

	public function getOption ($key) {
		return $this->options[$key];
	}

	public function getData ($key = null) {
		if ($key !== null) {
			if (isset($this->data[$this->index]->{$key})) {
				return $this->data[$this->index]->{$key};
			} elseif (isset($this->data[$this->index]) && isset($this->data[$this->index][$key])) {
				return $this->data[$this->index][$key];
			} else {
				return null;
			}
		}

		return $this->data;
	}

	public function getListId () {
		return $this->listId;
	}

	public function getClass () {
		return $this->class;
	}

	public function getDomData () {
		$domData = $this->domData;

		if ($domData === null) {
			return $domData;
		}

		$return = [];
		foreach ($domData as $key => $value) {
			$return[$key] = $this->replaceVars($value);
		}

		return $return;
	}

	public function getClassString () {
		$class = 'btn' . ($this->baseClass ? ' ' . $this->baseClass : null);

		if ($this->class !== null) {
			$class .= ' ' . $this->class;	
		}

		if ($class !== null) {
			return ' class="' . $class . '"';
		}

		return false;
	}

	public function getDomDataString () {
		$string = '';

		if ($this->getDomData() !== null) {
			foreach ($this->getDomData() as $key => $value) {
				$string .= 'data-' . $key . '="' . $value . '" ';
			}
		}

		return $string;
	}
}