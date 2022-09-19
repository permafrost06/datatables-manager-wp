<?php

/**
 * @var $router DtManager\App\Http\Router
 */

$router->post('/tables', 'TableController@addTable');
$router->get('/tables', 'TableController@getAllTables');
$router->get('/table/{table_id}', 'TableController@getTable');
$router->patch('/tables/{table_id}', 'TableController@updateTable');
$router->delete('/tables/{table_id}', 'TableController@deleteTable');

$router->post('/tables/{table_id}/rows', 'RowsController@addRow');
$router->get('/tables/{table_id}/rows', 'RowsController@getTableRows');

$router->patch('/rows/{row_id}', 'RowsController@updateRow');
$router->delete('/rows/{row_id}', 'RowsController@deleteRow');
