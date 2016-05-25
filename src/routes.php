<?php

Route::group(['middleware' => 'web'], function(){
	Route::post('/wax/repeater', 'Waxis\Repeater\RepeaterController@repeat');
	Route::post('/wax/repeater/more', 'Waxis\Repeater\RepeaterController@more');
	Route::post('/wax/repeater/delete', 'Waxis\Repeater\RepeaterController@delete');
	Route::post('/wax/repeater/edit', 'Waxis\Repeater\RepeaterController@edit');
	Route::post('/wax/repeater/changeorder', 'Waxis\Repeater\RepeaterController@changeorder');
});