<?php

namespace Waxis\Repeater;

class Repeater extends Repeater\Ancestor {

	public $type = 'list';

	public $id = null;
	
	public $class = null;

	public $permission = null;

	public $ownerField = null;

	public $descriptor = null;

	public $init = true;

	public $page = 1;

	public $template = 'table.phtml'; // default view

	public $fields = null;

	public $buttons = null;

	public $buttonsLabel = '&nbsp;';

	public $table = null;

	public $source = null;

	public $sourceAdapter = null;

	public $params = null;

	public $filters = null;

	public $filterValues = null;

	public $where = null;

	public $paginator = 'pages';

	public $autoload = false;

	public $limit = 10; // default limit

	public $displayPages = 3; // default pages to display

	public $order = 'DESC'; // default order

	public $orderBy = null; // default order by

	public $refresh = false;

	public $converter = null;

	public $convertBeforeOrder = false;

	public $instantiate = null;

	# name of the descriptor
	public function __construct($descriptor, $page = 1, $params = null) {
		if ($this->descriptor === null) {
			if (is_string($descriptor)) {
				$descriptorClass = '\App\Descriptors\Repeater\\' . ucfirst($descriptor);
				$descriptorObj = new $descriptorClass;
				$descriptor = $descriptorObj->descriptor($params);

				if (!isset($descriptor['params']) && $params !== null) {
					$descriptor['params'] = $params;
				}

				$this->descriptor = $descriptor;
			} else {
				$this->descriptor = $descriptor;
			}
		}

		$this->params = $params;

		if (isset($params['filters'])) {
			$this->filterValues = $params['filters'];
		}

		if (empty($page)) {
			$page = 1;
		}
		$this->page = $page;

		if (isset($descriptor['id'])) {
			$this->id = $descriptor['id'];
		}

		if (isset($descriptor['class'])) {
			$this->class = $descriptor['class'];
		}

		if (isset($descriptor['permission'])) {
			$this->permission = $descriptor['permission'];
		}

		if (isset($descriptor['ownerField'])) {
			$this->ownerField = $descriptor['ownerField'];
		}

		if (isset($descriptor['init'])) {
			$this->init = $descriptor['init'];
		}

		if (isset($descriptor['template'])) {
			$this->template = $descriptor['template'];
		}

		if (isset($descriptor['fields'])) {
			$this->fields = $descriptor['fields'];
		}

		if (isset($descriptor['buttons'])) {
			$this->buttons = $descriptor['buttons'];
		}

		if (isset($descriptor['buttonsLabel'])) {
			$this->buttonsLabel = $descriptor['buttonsLabel'];
		}

		if (isset($descriptor['table'])) {
			$this->table = $descriptor['table'];
		}

		if (isset($descriptor['source'])) {
			$this->source = $descriptor['source'];
		}

		if (!isset($descriptor['source']) && isset($descriptor['table'])) {
			$this->source = $descriptor['table'];
		}

		if (isset($descriptor['filters'])) {
			$this->filters = $descriptor['filters'];
		}

		if (isset($descriptor['where'])) {
			$this->where = $descriptor['where'];
		}

		if (isset($descriptor['paginator'])) {
			$this->paginator = $descriptor['paginator'];
		}

		if (isset($descriptor['autoload'])) {
			$this->autoload = $descriptor['autoload'];
		}

		if (isset($descriptor['limit'])) {
			$this->limit = $descriptor['limit'];
		}

		if (isset($descriptor['displayPages'])) {
			$this->displayPages = $descriptor['displayPages'];
		}

		if (isset($descriptor['order'])) {
			$this->order = $descriptor['order'];
		}

		if (isset($descriptor['orderBy'])) {
			$this->orderBy = $descriptor['orderBy'];
		}

		if (isset($descriptor['converter'])) {
			$this->converter = $descriptor['converter'];
		}

		if (isset($descriptor['convertBeforeOrder'])) {
			$this->convertBeforeOrder = $descriptor['convertBeforeOrder'];
		}

		if (isset($descriptor['instantiate'])) {
			$this->instantiate = $descriptor['instantiate'];
		}

		if (isset($params['limit'])) {
			$this->limit = $params['limit'];
		}

		if (isset($params['order'])) {
			$this->order = $params['order'];
		}

		if (isset($params['orderBy'])) {
			$this->orderBy = $params['orderBy'];
		}

		parent::__construct($descriptor);
	}

	public function refresh () {
		$this->refresh = true;
	}

	public function getPaginator ($a = true) {
		if (!isset($this->paginator['type'])) {
			$this->paginator = [
				'type' => 'pages'
			];
		}
		
		$options = array(
			'limit' => $this->limit,
			'total' => $this->getSourceAdapter()->getTotalCount(),
			'page' => $this->page,
			'displayPages' => $this->displayPages,
			'autoload' => $this->getAutoload(),
			'init' => $this->getInit(),
			'listId' => $this->getId(),
		);

		switch ($this->paginator['type']) {
			case 'more';
				$options['refresh'] = $this->refresh;

				$paginator = new Repeater\Paginator\More($this->paginator, $options);
				break;

			case 'pages';
				$paginator = new Repeater\Paginator\Pages($this->paginator, $options);
				break;
		}
		
		return $paginator;
	}

	public function getPaginatorType () {
		$this->getPaginator(false);

		return $this->paginator['type'];
	}

	public function getLabels () {
		return $this->getFieldInstances();
	}

	public function getRows () {
		$rows = array();

		$data = $this->getData();

		if ($this->getConverter() !== null) {
			$data = call_user_func($this->converter, $data);
		}

		foreach ($data as $key => $row) {
			$rows[$key] = $this->getFieldInstances($row);
		}

		return $rows;
	}

	# Row is the row filled with data
	public function getFieldInstance ($field, $row = null) {
		if (!isset($field['type'])) {
			$field['type'] = false;
		}

		$field['order'] = $this->order;
		$field['orderBy'] = $this->orderBy;
		$field['table'] = $this->table;

		$return = null;

		$fieldName = $field['name'];
		$value = $row !== null && isset($row->$fieldName) ? $row->$fieldName : null;

		switch ($field['type']) {
			case 'image':
				$return = new Repeater\Field\Image($field, $value, $row);
				break;

			case 'order':
				$return = new Repeater\Field\Order($field, $value, $row);
				break;

			default:
				$return = new Repeater\Field\Td($field, $value, $row);
		}

		return $return;
	}

	public function getFieldInstances ($row = null) {
		$instances = array();

		foreach ($this->fields as $field) {
			$instances[] = $this->getFieldInstance($field, $row);
		}
		return $instances;
	}

	public function convertField($field, $value)
	{
		$field = $this->getFieldFromDescriptor($field);

		if ($field === null || !isset($field['convert'])) {
			return $value;
		}

		$converter = explode('::',$field['convert']['converter']);
		$class = $converter[0];
		$method = $converter[1];

		# @todo

		$options = getValue($field['convert'], 'options', array());

		$params = array_merge(array($value), $options);

		return call_user_func_array($class . '::' . $method, $params);
	}

	public function getFieldFromDescriptor($field)
	{
		$return = array();

		foreach ($this->descriptor['fields'] as $one) {
			if ($one['name'] == $field) {
				return $one;
			}
		}

		return null;
	}

	public function getData () {
		if ($this->isConvertBeforeOrder() && !$this->getSourceAdapter() instanceof Repeater\Source\Batch) {
			$source	= clone $this->getSourceAdapter(true, true);

			$this->source = call_user_func($this->converter, $source->getData());
			$source	= clone $this->getSourceAdapter(false, true);
		} else {
			$source	= clone $this->getSourceAdapter();
		}

		$source->setLimit($this->limit);
		$source->setPage($this->page);
 
		$data = $source->getData();
		
		if ($this->getInstantiate()) {
			$data = $this->convertToInstantiate($data);
		}

		return $data;
	}

	public function convertToInstantiate ($data) {
		$instantiate = $this->getInstantiate();
		$return = array();

		foreach ($data as $key => $one) {
			$return[$key] = $instantiate::find($one->id);
		}

		return $return;
	}

	public function isConvertBeforeOrder () {
		return $this->convertBeforeOrder;
	}

	public function setFilterValues ($filterValues) {
		$this->filterValues = $filterValues;
	}

	public function setOrder ($order) {
		$this->order = $order;
	}

	public function setOrderBy ($orderBy) {
		$this->orderBy = $orderBy;
	}

	public function setLimit ($limit) {
		$this->limit = $limit;
	}

	public function getId () {
		return $this->id;
	}

	public function getPermission () {
		return $this->permission;
	}

	public function isPermitted ($recordId = false) {
		# @todo: megírni

		/*if ($this->getPermission() === null) {
			return true;
		}

		$class = APP_NAME . '_' . ucfirst($this->getPermission());

		if ($class::getInstance()->isLoggedIn()) {
			if ($this->getOwnerField() !== null && $recordId) {
				$db = Zend_Registry::get('db');
				$query = $db->select()
					->from($this->getTable())
					->where('id = "' . $recordId . '"');
				$results = $db->query($query)->fetchAll();
				if (isset($results[0]) && $results[0][$this->getOwnerField()] == $class::getInstance()->getId()) {
					return true;
				}

				return false;
			} else {
				return true;
			}
		}

		return false;*/

		return true;
	}

	public function getOwnerField () {
		return $this->ownerField;
	}

	public function getInit () {
		return $this->init;
	}

	public function getPage () {
		return $this->page;
	}

	public function getFields () {
		return $this->fields;
	}

	public function getField ($name) {
		foreach ($this->fields as $field) {
			if ($field['name'] == $name) {
				return $field;
			}
		}
	}

	public function getFieldByType ($type) {
		foreach ($this->fields as $field) {
			if (isset($field['type']) && $field['type'] == $type) {
				return $field;
			}
		}
	}

	# $index = index of current row
	public function getButtons ($index = null) {

		$data = $this->getData();

		$buttons = array();

		foreach ($this->buttons as $button) {
			if (!isset($button['type'])) {
				$button['type'] = null;
			}

			$button['listId'] = $this->id;
			$button['table'] = $this->table;

			switch ($button['type']) {
				case 'delete':
					$instance = new Repeater\Button\Delete($button, $data, $index);
					break;

				default:
					$instance = new Repeater\Button\Ancestor($button, $data, $index);
			}

			$buttons[] = $instance->fetch();
		}

		$buttons = new Repeater\Field\Buttons(null, implode('&nbsp;',$buttons));

		return $buttons;
	}

	public function hasButtons () {
		if ($this->buttons === null) {
			return false;
		}
		
		return true;
	}

	public function getButtonsLabel () {
		$label = $this->buttonsLabel;

		return new Repeater\Field\Th(array('label' => $label, 'orderDisabled' => true));
	}

	public function getTable () {
		return $this->table;
	}

	public function getSource () {
		return $this->source;
	}

	public function getSourceAdapter ($disableOrder = false, $forceRefresh = false) {
		\DB::enableQueryLog();
		if ($this->sourceAdapter === null || $forceRefresh) {
			$options = array();

			if (!$disableOrder) {
				$options['order'] = $this->order;
				$options['orderBy'] = $this->orderBy;
			}

			# if it's a string then it's a table name
			if (is_string($this->source)) {
				$users = \DB::table('users')->get();
				$sourceAdapter = new Repeater\Source\Db($this->source, $options);

			# if it's an array can be an array of data or class method
			} elseif (is_array($this->source)) {

				# if class param exists it's a class method call
				if (isset($this->source['class'])) {
					$source = $this->getDataSourceFromClassMethod($this->source);

					# treat as laravel query
					if ($source instanceof \Illuminate\Database\Query\Builder) {
						$sourceAdapter = new Repeater\Source\Query($source, $options);		

					# treat as array
					} else {
						$sourceAdapter = new Repeater\Source\Batch($source, $options);	
					}

				# if it's an array of elements
				} else {
					$sourceAdapter = new Repeater\Source\Batch($this->source, $options);
				}

			# if it's a laravel query
			}
			elseif ($this->source instanceof \DB) {
				$sourceAdapter = new Repeater\Source\Query($this->source, $options);
			}

			$sourceAdapter->where = $this->where;
			$sourceAdapter->filters = $this->filters;
			$sourceAdapter->filterValues = $this->filterValues;

			if ($this->refresh) {
				$sourceAdapter->fromZero();
				$sourceAdapter->refresh();
			}

			$this->sourceAdapter = $sourceAdapter;
		}

		return $this->sourceAdapter;
	}

	public function getDataSourceFromClassMethod ($source) {
		$class = $source['class'];
		$method = $source['method'];

		$static = getValue($source, 'static');
		$instantiate = getValue($source, 'instantiate');

		$static = false;
		$instantiate = false;

		if ($static) {
			$result = $class::$method($this->params, $this);
		} elseif ($instantiate) {
			$result = $class::getInstance()->$method($this->params, $this);
		} else {
			$instance = new $class($this->params, $this);
			$result = $instance->$method($this->params, $this);
		}

		return $result;
	}

	public function getAutoload () {
		return $this->autoload;
	}

	public function getLimit () {
		return $this->limit;
	}

	public function getConverter () {
		return $this->converter;
	}

	public function getInstantiate () {
		return $this->instantiate;
	}

	public function getClass () {
		return $this->class;
	}

	public function getDescriptor () {
		return $this->descriptor;
	}
}