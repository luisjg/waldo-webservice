<?php

use App\Http\Controllers\RoomsController;

class RoomsControllerTest extends TestCase
{
    protected $roomsController;
    protected $roomID;
    public function setUp(){
        parent::setUp();
        $this->roomsController = new RoomsController;
        $this->roomID = 'JD2211';
    }
    public function testGetRoom_returns_room(){
        $data = $this->roomsController->getRoom($this->roomID);
        $this->assertEquals($data['status'],'200');
        $this->assertEquals($data['success'],'true');
        $this->assertEquals($data['collection'],'room');
        $this->assertEquals($data['room']['room_number'],$this->roomID);
        $this->assertEquals($data['room']['building_name'],'Jacaranda Hall');
        $this->assertEquals($data['room']['latitude'],34.24141145);
        $this->assertEquals($data['room']['longitude'],-118.529299945);
    }
    public function testGetAllRooms_returns_all_rooms(){
        $data = $this->roomsController->getAllRooms();
        $this->assertEquals($data['status'],'200');
        $this->assertEquals($data['success'],'true');
        $this->assertEquals($data['collection'],'rooms');
        $this->assertEquals(count($data['rooms']),5944);
    }
    public function testHandleRequest_returns_room(){
        $data = $this->call('GET', 'api/1.0/rooms?room=' . $this->roomID);
        $content = json_decode($data->content(), true);
        $this->assertEquals($content['status'],200);
        $this->assertEquals($content['collection'],'room');
        $this->assertArrayHasKey('room',$content);
    }
    public function testHandleRequest_returns_all_rooms(){
        $data = $this->call('GET', 'api/1.0/rooms');
        $content = json_decode($data->content(), true);
        $this->assertEquals(count($content['rooms']),count($this->roomsController->getAllRooms()['rooms']));
        $this->assertEquals($content['status'],$this->roomsController->getAllRooms()['status']);
    }
    public function testHandleRequest_returns_error_array(){
        $data = $this->call('GET', 'api/1.0/rooms?invalid=invalid');
        $content = json_decode($data->content(), true);
        $this->assertEquals($content['status'],400);
        $this->assertEquals($content['success'],'false');
        $this->assertArrayHasKey('errors',$content);
    }
}