<?php

$router->group(['prefix' => '1.0'], function() use ($router) {
    $router->get('/rooms', 'RoomsController@handleRequest');
});