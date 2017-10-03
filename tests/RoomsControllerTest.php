<?php

use App\Http\Controllers\RoomsController;

class RoomsControllerTest extends TestCase
{
    protected $roomsController;
    public function setUp(){
        $this->roomsController = new RoomsController;
    }
    public function testGetAllRooms_returns_all_rooms(){
        $data = $this->roomsController->getAllRooms();
        $this->assertEquals($data['status'],'200');
        $this->assertEquals($data['success'],'true');
        $this->assertEquals($data['collection'],'rooms');
        $this->assertEquals(count($data['rooms']),5944);
    }
}
