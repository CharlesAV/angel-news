<?php

Route::group(array('prefix' => 'news'), function() {

	$controller = 'NewsController';

	Route::get('/', $controller . '@index');
	Route::get('{slug}', $controller . '@show');
	Route::get('archive/{year}/{month}', $controller . '@archive');
});

Route::group(array('prefix' => admin_uri('news'), 'before' => 'admin'), function() {

	$controller = 'AdminNewsController';

	Route::get('/', $controller . '@index');
	Route::get('add', $controller . '@add');
	Route::post('add', array(
		'before' => 'csrf',
		'uses' => $controller . '@attempt_add'
	));
	Route::get('edit/{id}', $controller . '@edit');
	Route::post('edit/{id}', array(
		'before' => 'csrf',
		'uses' => $controller . '@attempt_edit'
	));
	Route::post('delete/{id}', array(
		'before' => 'csrf',
		'uses' => $controller . '@delete'
	));
});