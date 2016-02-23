<?php

Route::post('/wax/repeater', 'Waxis\Lister\Controllers\ListerController@list');
Route::post('/wax/repeater/more', 'Waxis\Lister\Controllers\ListerController@more');
Route::post('/wax/repeater/delete', 'Waxis\Lister\Controllers\ListerController@delete');
Route::post('/wax/repeater/changeorder', 'Waxis\Lister\Controllers\ListerController@changeorder');