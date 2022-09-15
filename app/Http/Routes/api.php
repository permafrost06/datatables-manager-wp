<?php

/**
 * @var $router DtManager\App\Http\Router
 */

$router->get('/tables', 'TableController@getAllTables');
$router->get('/table/{table_id}', 'TableController@getTable');
$router->post('/tables', 'TableController@addTable');
$router->delete('/tables/{table_id}', 'TableController@deleteTable');
