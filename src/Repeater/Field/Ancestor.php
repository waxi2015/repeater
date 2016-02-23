<?php

namespace Wax\Repeater\Repeater\Field;

class Ancestor extends \Wax\Repeater\Repeater\Ancestor {

	public $type = 'field';

	public $name = null;

	public $label = null;

	public $labelTemplate = 'th.phtml';

	public $width = null;

	public $value = null;

	public $baseClass = null;

	public $class = null;

	public $align = null;
	
	public $style = null;

	public $options = null;

	public $template = null;

	public $convert = null;

	public $table = null;

	public $row = null;

	public $order = null;

	public $orderBy = null;

	public $orderDisabled = false;

	public $orderSignAsc = ' <span class="fa fa-caret-up"> ';

	public $orderSignDesc = ' <span class="fa fa-caret-down"> ';

	public function __construct ($descriptor, $value = null, $row = null) {
		$this->row = $row;

		if (isset($descriptor['name'])) {
			$this->name = $descriptor['name'];
		}

		if (isset($descriptor['label'])) {
			$this->label = $descriptor['label'];
		}
		
		if (isset($descriptor['width'])) {
			$this->width = $descriptor['width'];
		}
		
		if (isset($descriptor['class'])) {
			$this->class = $descriptor['class'];
		}
		
		if (isset($descriptor['align'])) {
			$this->align = $descriptor['align'];
		}
		
		if (isset($descriptor['options'])) {
			$this->options = $descriptor['options'];
		}

		if (isset($descriptor['template'])) {
			$this->template = $descriptor['template'];
		}

		if (isset($descriptor['convert'])) {
			$this->convert = $descriptor['convert'];
		}

		if (isset($descriptor['table'])) {
			$this->table = $descriptor['table'];
		}

		if (isset($descriptor['order'])) {
			$this->order = $descriptor['order'];
		}

		if (isset($descriptor['orderBy'])) {
			$this->orderBy = $descriptor['orderBy'];
		}

		if (isset($descriptor['orderDisabled'])) {
			$this->orderDisabled = $descriptor['orderDisabled'];
		}

		if ($value !== null) {
			$this->value = $value;
		}

		parent::__construct($descriptor);
	}

	public function renderLabel () {
		echo $this->fetchLabel();
	}

	public function fetchLabel () {
		return $this->fetch($this->labelTemplate);
	}

	public function getWidthString () {
		if ($this->width === null) {
			return false;
		}

		return ' width="' . $this->width . '"';
	}

	public function getClassString () {
		$class = $this->baseClass;

		if ($this->class !== null) {
			$class = ' ' . $this->class;	
		}

		if ($class !== null) {
			return ' class="' . $class . '"';
		}

		return false;
	}

	public function convert ($value) {
		if ($this->convert === null) {
			return $value;
		}

		$converter = explode('::',$this->convert['converter']);
		$class = $converter[0];
		$method = $converter[1];

		$options = getValue($this->convert, 'options', array());

		$params = array_merge(array($value), $options);

		return call_user_func_array($class . '::' . $method, $params);
	}

	public function getData ($key = null) {
		$data = $this->row;

		if ($key !== null) {
			$data = $data->{$key};
		}

		return $data;
	}

	public function getIndex () {
		return $this->index;
	}

	public function getName () {
		return $this->name;
	}

	public function getLabel () {
		return $this->label;
	}

	public function getWidth () {
		return $this->width;
	}

	public function getValue () {
		return $this->convert($this->value);
	}

	public function getClass () {
		return $this->class;
	}

	public function getAlign () {
		return $this->align;
	}

	public function getAlignString () {
		if ($this->align === null) {
			return false;
		}

		return ' align="' . $this->align . '"';
	}

	public function getStyle () {
		return $this->style;
	}

	public function getStyleString () {
		if ($this->style === null) {
			return false;
		}

		return ' style="' . $this->style . '"';
	}

	public function getOptions () {
		return $this->options;
	}

	public function getOption ($key) {
		return isset($this->options[$key]) ? $this->options[$key] : null;
	}

	public function getConvert () {
		return $this->convert;
	}

	public function getTable () {
		return $this->table;
	}

	public function getOrderBy () {
		return $this->orderBy;
	}

	public function getOrder () {
		return $this->order;
	}

	public function getOrderDisabled () {
		return $this->orderDisabled;
	}

	public function getOrderDomDataString () {
		$by = $this->getName();
		$order = 'ASC';

		if ($this->getOrderBy() !== null && $this->getOrderBy() == $by && $this->getOrder() == 'ASC') {
			$order = 'DESC';
		}

		return 'data-by="' . $by . '" data-order="' . $order . '"';
	}

	public function isOrderedByThis () {
		if ($this->getOrderBy() !== null && $this->getOrderBy() == $this->getName()) {
			return true;
		}

		return false;
	}

	public function getOrderSign () {
		if ($this->isOrderedByThis()) {
			if ($this->getOrder() == 'ASC') {
				return $this->orderSignAsc;
			} else {
				return $this->orderSignDesc;
			}
		}
	}
}