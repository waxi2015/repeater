<?php

Route::post('/wax/repeater', 'Wax\Lister\Controllers\ListerController@list');
Route::post('/wax/repeater/more', 'Wax\Lister\Controllers\ListerController@more');
Route::post('/wax/repeater/delete', 'Wax\Lister\Controllers\ListerController@delete');
Route::post('/wax/repeater/changeorder', 'Wax\Lister\Controllers\ListerController@changeorder');