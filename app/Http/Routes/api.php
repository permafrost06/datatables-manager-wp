<?php

/**
 * @var $router DtManager\App\Http\Router
 */

$router->get('/getTables', 'TableController@getAllTables');

$router->get('/getTable', 'TableController@getTable');

$router->post('/createTable', 'TableController@addTable');

$router->post('/deleteTable', 'TableController@deleteTable');
