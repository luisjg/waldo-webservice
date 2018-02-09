<?php

$router->group(['prefix' => '1.0'], function() use ($router) {
	$router->get('/rooms/sync', 'RoomsController@syncRoomCoordinates');
    $router->get('/rooms', 'RoomsController@handleRequest');
});