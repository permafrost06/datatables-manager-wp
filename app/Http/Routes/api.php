<?php

/**
 * @var $router DtManager\App\Http\Router
 */

$router->post('/tables', 'TableController@store');
$router->get('/tables', 'TableController@index');
$router->get('/table/{table_id}', 'TableController@show');
$router->patch('/tables/{table_id}', 'TableController@update');
$router->delete('/tables/{table_id}', 'TableController@destroy');

$router->post('/tables/{table_id}/rows', 'RowsController@store');
$router->get('/tables/{table_id}/rows', 'RowsController@index');

$router->patch('/rows/{row_id}', 'RowsController@update');
$router->delete('/rows/{row_id}', 'RowsController@destroy');

$router->get('/datatable', 'DatatableController@index');
