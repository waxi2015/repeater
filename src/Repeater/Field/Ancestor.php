<?php

namespace Waxis\Repeater\Repeater\Field;

class Ancestor extends \Waxis\Repeater\Repeater\Ancestor {

	public $type = 'field';
	
	public $listId = null;

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

	public $valueFormat = null;

	public $source = null;

	public $href = null;

	public $clickable = null;

	public function __construct ($descriptor, $value = null, $row = null) {
		$this->row = $row;

		if (isset($descriptor['name'])) {
			$this->name = $descriptor['name'];
		}

		if (isset($descriptor['listId'])) {
			$this->listId = $descriptor['listId'];
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

		if (isset($descriptor['value'])) {
			$this->valueFormat = $descriptor['value'];
		}

		if (isset($descriptor['source'])) {
			$this->source = $descriptor['source'];
		}

		if (isset($descriptor['href'])) {
			$this->href = $descriptor['href'];
		}

		if (isset($descriptor['clickable'])) {
			$this->clickable = $descriptor['clickable'];
		}

		if ($value !== null) {
			$this->value = $value;
		}

		parent::__construct($descriptor);
	}

	public function renderLabel () {
		echo $this->fetchLabel();
	}

	public function getHref () {
		if ($this->href !== null) {
			return $this->href;
		}

		$row = to_array($this->row);

		# work with cms, assuming that list id is same as tab name
		if ($this->clickable !== null) {
			return config('cms.url') . '/' . $this->getListId() . '/edit/' . $row['id']; 
		}

		return false;
	}

	public function isClickable () {
		if ($this->href !== null || $this->clickable !== null) {
			return true;
		}

		return false;
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
		if($this->valueFormat !== null && preg_match_all('/{+(.*?)}/', $this->valueFormat, $matches)) {
			$value = $this->valueFormat;
			$row = to_array($this->row);
			foreach ($matches[1] as $match) {
		    	$value = str_replace('{'.$match.'}', $row[$match], $value);
			}
		}

		if ($this->source !== null) {
			$source = $this->source;

			if (isset($source['class']) && isset($source['method'])) {
				$sourceClass = $source['class'];
				$sourceMethod = $source['method'];
				$source = new $sourceClass;
				$source = $source->$sourceMethod();
			}

			if (!is_array($source) && strstr($source, '::')){
				$source = explode('::',$source);
				$sourceClass = $source[0];
				$sourceMethod = $source[1];
				$source = call_user_func($sourceClass . '::' . $sourceMethod);
			}

			$value = isset($source[$value]) ? $source[$value] : $value;
		}

		if ($this->convert === null) {
			return $value;
		} elseif (isset($this->convert['converter'])) {
			$converter = explode('::',$this->convert['converter']);
			$class = $converter[0];
			$method = $converter[1];

			$options = getValue($this->convert, 'options', array());

			$params = array_merge(array($value), $options);

			return call_user_func_array($class . '::' . $method, $params);
		} elseif (isset($this->convert[$value])) {
			return $this->convert[$value];
		} else {
			return $value;
		}
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
		return trans($this->label);
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
		if ($this->align !== null) {
			$this->style .= 'text-align: ' . $this->align . ';';
		}

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

	public function getListId () {
		return $this->listId;
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