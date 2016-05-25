<?php

namespace Waxis\Repeater;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class RepeaterController extends Controller
{
	public function __construct (Request $request) {
		if (isset($request->locale)) {
			\Lang::setLocale($request->locale);
		}
	}

    public function changeorder (Request $request) {
    	$descriptor = unserialize(decode($request->descriptor));
		$id = $request->id;
		$newOrder = $request->order;
		$repeater = new \Repeater($descriptor);

		if (!$repeater->isPermitted($id)) {
			return array();
		}

		$field = new \Waxis\Repeater\Repeater\Field\Order($repeater->getFieldByType('order'));

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

		$response['message'] = trans('repeater.order_success_msg');

		return $response;
	}

    public function edit (Request $request) {
    	$descriptor = unserialize(decode($request->descriptor));
		$id = $request->id;

		$repeater = new \Repeater($descriptor);

		if (!$repeater->isPermitted($id)) {
			return array();
		}

		$form = new \Form($repeater->getFormDescriptor('edit'), $id);

		$response['html'] = $form->fetch();

		return $response;
	}

	public function delete (Request $request) {
    	$descriptor = unserialize(decode($request->descriptor));
		$id = $request->id;
		$repeater = new \Repeater($descriptor);

		if (!$repeater->isPermitted($id)) {
			return array();
		}

		$table = $repeater->getTable();

		$orderField = $repeater->getFieldByType('order');
		if ($orderField) {
			$orderField = new \Waxis\Repeater\Repeater\Field\Order($orderField);
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

		$response['message'] = trans('repeater.delete_success_msg');

		return $response;
	}

	public function more (Request $request) {
		$descriptor = unserialize(decode($request->descriptor));
		$page = $request->page;
		$params = $request->params;
		$refresh = $request->refresh;
		$repeater = new \Repeater($descriptor, $page, $params);

		if (!$repeater->isPermitted()) {
			return array();
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

	public function repeat (Request $request) {
		$descriptor = unserialize(decode($request->descriptor));
		$page = $request->page;
		$params = $request->params;
		$repeater = new \Repeater($descriptor, $page, $params);

		if (!$repeater->isPermitted()) {
			return array();
		}

		$response['html'] = $repeater->fetch();

		return $response;
	}
}
