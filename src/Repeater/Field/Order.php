<?php

namespace Wax\Repeater\Repeater\Field;

class Order extends Td {

	public $template = 'order.phtml';

	public $baseClass = 'wax-repeater-order';

	public $style = 'text-align: right;';

	public $width = '1%';

	public function getValue () {
		$table = $this->getTable();

		$descriptor = array(
			$this->getTable() => array(
				'table' => $table,
				'orderColumn' => $this->getName()
			)
		);

		$order = new \Order($table);

		$params = $this->getOption('params');
		if ($params !== null) {
			$descriptor[$table]['searchParams'] = $params;

			$searchParams = array();

			foreach ($params as $param) {
				$searchParams[$param] = $this->getData($param);
			}

			$order->setSearchParams($searchParams);
		}

		$order->setDescriptor($descriptor);
		return $order->createSelect2($this->getData('id'), $this->getData($this->getName()));
	}
}