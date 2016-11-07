<?php

namespace Waxis\Repeater;

class Repeater extends Repeater\Ancestor {

	public $type = 'list';

	public $id = null;
	
	public $class = null;

	public $permission = null;

	public $ownerField = null;

	public $guard = null;

	public $descriptor = null;

	public $init = true;

	public $initScript = null;

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

	public $emptyText = 'repeater.empty_text';

	public $vars = [];

	public $form = null;

	public $actions = null;

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

		if (isset($descriptor['guard'])) {
			$this->guard = $descriptor['guard'];
		}

		if (isset($descriptor['init'])) {
			$this->init = $descriptor['init'];
		}

		if (isset($descriptor['initScript'])) {
			$this->initScript = $descriptor['initScript'];
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

		if (isset($descriptor['pages'])) {
			$this->displayPages = $descriptor['pages'];
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

		if (isset($descriptor['emptyText'])) {
			$this->emptyText = $descriptor['emptyText'];
		}

		if (isset($descriptor['vars'])) {
			$this->vars = $descriptor['vars'];
		}

		if (isset($descriptor['form'])) {
			$this->form = $descriptor['form'];
		}

		if (isset($descriptor['actions'])) {
			$this->actions = $descriptor['actions'];
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

	public function getInitScripts () {
		$scripts = $this->initScript;
		if ($scripts === null) {
			return [];
		}

		if (!isset($scripts[0])) {
			$scripts = [$scripts];
		}

		return $scripts;
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

	# $createFields: if it should create table fields
	# or leave the raw data
	public function getRows ($createFields = true) {
		$rows = array();

		$data = $this->getData();

		if ($this->getConverter() !== null) {
			$data = call_user_func($this->converter, $data);
		}

		foreach ($data as $key => $row) {
			if ($createFields) {
				$rows[$key] = $this->getFieldInstances($row);
			} else {
				$rows[$key] = to_array($row);
			}
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
		$field['listId'] = $this->id;

		$return = null;

		$fieldName = $field['name'];
		$value = $row !== null && isset($row->$fieldName) ? $row->$fieldName : null;

		if ($value === null && $row !== null && is_array($row) && isset($row[$fieldName])) {
			$value = $row[$fieldName];
		}

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
		//dd(\DB::getQueryLog());
		
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

	public function getGuard ($action = null) {
		if ($this->guard === null) {
			return null;
		}

		if ($action !== null) {
			if (isset($this->guard[$action])) {
				return $this->guard[$action];
			} else {
				return null;
			}
		} else {
			if (is_array($this->guard)) {
				return null;
			} else {
				return $this->guard;
			}
			
		}

	}

	public function isPermitted ($recordId = false, $action = null) {
		$return = true;

		if ($this->getPermission() !== null) {
			if (\Auth::guard($this->getPermission())->check()) {
				if ($this->getOwnerField() !== null && $recordId) {
					$result = collect(\DB::table($this->getTable())
						->where('id', $recordId)->first())->toArray();

					if (isset($result[$this->getOwnerField()]) && $result[$this->getOwnerField()] == \Auth::guard('admin')->user()->id) {
						$return = true;
					} else {
						$return = false;
					}
				} else {
					$return = true;
				}
			} else {
				$return = false;
			}
		}

		if ($return === true && $this->getGuard($action) !== null) {
			$guard = $this->getGuard($action);
			$return = $guard($recordId);
		}

		return $return;
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

		if ($this->getConverter() !== null) {
			$data = call_user_func($this->converter, $data);
		}
		
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

				case 'edit':
					$instance = new Repeater\Button\Edit($button, $data, $index);
					break;

				case 'editpopup':
					$instance = new Repeater\Button\Editpopup($button, $data, $index);
					break;

				default:
					$instance = new Repeater\Button\Ancestor($button, $data, $index);
			}

			if ($instance->hasPermission()) {
				$buttons[] = $instance->fetch();
			}
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

	public function getEmptyText () {
		return trans($this->emptyText);
	}

	public function getDescriptor () {
		return $this->descriptor;
	}

	public function getFormDescriptor ($type = 'add') {
		$descriptor = $this->form['descriptor'];

		if (is_string($descriptor)) {
			$descriptor = '\App\Descriptors\Form\\' . ucfirst($descriptor);

			if (class_exists($descriptor . '_' . strtolower($type))) {
				$descriptor .= '_' . strtolower($type);
			}

			$descriptor = new $descriptor;
			$descriptor = $descriptor->descriptor();
		}

		if (!isset($descriptor['feedback']) || !isset($descriptor['feedback']['false'])) {
			$descriptor['feedback']['false'] = [
				'valid' => false
			];
		}

		if (!isset($descriptor['feedback']) || !isset($descriptor['feedback']['true'])) {
			$descriptor['feedback']['true'] = [
				'valid' => true,
				'message' => 'repeater.success_process',
				'params' => ['repeaterId' => $this->getId()]
			];
		}

		if (!isset($descriptor['data'])) {
			$descriptor['data'] = [];
		}

		$descriptor['data']['success'] = 'waxrepeater.refreshAfterFormSave';

		if (!array_key_exists('save', $descriptor)) {
			$descriptor['save'] = true;
		}
		$descriptor['table'] = $this->getTable();

		if (isset($descriptor['id'])) {
			$descriptor['id'] = $descriptor['id'] . '-' . $type;
		} else {
			$descriptor['id'] = $this->getId() . '-' . $type;
		}

		$structureTypes = ['sections','brows','bcolumns','rows','columns','elements'];
		$formStructureType = 'elements';

		foreach ($structureTypes as $type) {
			if (isset($descriptor[$type])) {
				$formStructureType = $type;
			}
		}

		$addon = [
			'class' => 'hidden',
			'type' => 'hidden',
			'name' => 'id',
		];

		if ($formStructureType != 'elements') {
			$addon = [
				'class' => 'hidden',
				'elements' => [$addon]
			];
		}

		$descriptor[$formStructureType][] = $addon;

		return $descriptor;
	}

	public function getForm ($type = 'add') {
		return new \Form($this->getFormDescriptor($type), $this->vars);
	}

	public function isAdd () {
		if ($this->actions !== null && in_array('add',$this->actions)) {
			return true;
		}

		return false;
	}

	public function isEdit () {
		if ($this->actions !== null && in_array('edit',$this->actions)) {
			return true;
		}

		return false;
	}

	public function getFormLabel () {
		if (isset($this->form['labels'])) {
			return $this->descriptor['form']['labels'];
		}

		return false;
	}
}