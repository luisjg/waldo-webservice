<?php

$router->get('/rooms/sync', 'RoomsController@syncRoomCoordinates');
$router->get('/rooms', 'RoomsController@handleRequest');