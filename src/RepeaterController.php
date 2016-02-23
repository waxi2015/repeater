<?php

namespace Wax\Repeater\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class RepeaterController extends Controller
{

    public function changeorder (Request $request) {
    	$descriptor = unserialize(decode($request->descriptor));
		$id = $request->id;
		$newOrder = $request->order;
		$repeater = new \Repeater($descriptor);

		if (!$repeater->isPermitted($id)) {
			$this->_forward('error', null, null, array('errorCode' => 901));
			return false;
		}

		$field = new \Wax\Repeater\Repeater\Field\Order($repeater->getFieldByType('order'));

		$table = $repeater->getTable();

		$descriptor = array(
			$table => array(
				'table' => $table,
				'orderColumn' => $field->getName()
			)
		);

		$order = new \Order($table);

		$params = $field->getOption('params');
		if ($params !== null) {
			$descriptor[$table]['searchParams'] = $params;
		}

		$order->setDescriptor($descriptor);
		
		$order->changeOrder($id, $newOrder);

		$response['message'] = 'Sorrend módosítás sikeres';

		return $response;
	}

	public function delete (Request $request) {
    	$descriptor = unserialize(decode($request->descriptor));
		$id = $request->id;
		$repeater = new \Repeater($descriptor);

		if (!$repeater->isPermitted($id)) {
			$this->_forward('error', null, null, array('errorCode' => 901));
			return false;
		}

		$table = $repeater->getTable();

		$orderField = $repeater->getFieldByType('order');
		if ($orderField) {
			$orderField = new \Wax\Repeater\Repeater\Field\Order($orderField);
			$descriptor = array(
				$table => array(
					'table' => $table,
					'orderColumn' => $orderField->getName()
				)
			);

			$order = new \Order($table);

			$params = $orderField->getOption('params');
			if ($params !== null) {
				$descriptor[$table]['searchParams'] = $params;
			}

			$order->setDescriptor($descriptor);
			
			$order->removeOrder($id);
		}

		$record = \DB::table($table)->where('id', $id)->first();

		$connector = false;
		if (isset($record->connector)) {
			$connector = $record->connector;
			\DB::table($table)->where('connector', $connector)->delete();
		} else {
			\DB::table($table)->where('id', $id)->delete();
		}

		$response['message'] = 'Törlés sikeres';

		return $response;
	}

	public function more (Request $request) {
		$descriptor = unserialize(decode($request->descriptor));
		$page = $request->page;
		$params = $request->params;
		$refresh = $request->refresh;
		$repeater = new \Repeater($descriptor, $page, $params);

		if (!$repeater->isPermitted()) {
			$this->_forward('error', null, null, array('errorCode' => 901));
			return false;
		}

		if (!empty($refresh)) {
			$repeater->refresh();
		}

		if (!empty($ahead)) {
			$repeater->ahead($ahead);
		}

		$response['html'] = $repeater->fetch();

		return $response;
	}

	public function list (Request $request) {
		$descriptor = unserialize(decode($request->descriptor));
		$page = $request->page;
		$params = $request->params;
		$repeater = new \Repeater($descriptor, $page, $params);

		if (!$repeater->isPermitted()) {
			$this->_forward('error', null, null, array('errorCode' => 901));
			return false;
		}

		$response['html'] = $repeater->fetch();

		return $response;
	}
}
