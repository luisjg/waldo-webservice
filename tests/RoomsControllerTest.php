<?php

use App\Http\Controllers\RoomsController;
use Illuminate\Support\Str;

class RoomsControllerTest extends TestCase
{
    protected $roomsController;
    protected $roomID;
    protected $lat;
    protected $lon;
    public function setUp(){
        parent::setUp();
        $this->roomsController = new RoomsController;
        $this->roomID = 'JD2211';
        $this->lat='34.2414114500';
        $this->lon='-118.5292999450';
    }

    public function testJsonHeader_returns_header(){
        $data = $this->roomsController->getRoom($this->roomID);
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('api', $data);
        $this->assertArrayHasKey('version', $data);
        $this->assertArrayHasKey('collection', $data);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('rooms', $data);
    }
    public function testGetRoom_returns_room(){
        $data = $this->roomsController->getRoom($this->roomID);
        $this->assertEquals($data['status'],200);
        $this->assertEquals($data['success'],'true');
        $this->assertEquals($data['collection'],'rooms');
        $this->assertEquals($data['rooms'][0]['room_number'],$this->roomID);
        $this->assertEquals($data['rooms'][0]['building_name'],'Jacaranda Hall');
        $this->assertEquals($data['rooms'][0]['latitude'],$this->lat);
        $this->assertEquals($data['rooms'][0]['longitude'],$this->lon);
    }
    public function testGetRoom_returns_error(){
        $data = $this->roomsController->getRoom('JD99999');
        $this->assertEquals($data['status'],404);
        $this->assertEquals($data['success'],'false');
        $this->assertArrayHasKey('errors',$data);
    }
    public function testGetAllRooms_returns_all_rooms(){
        $data = $this->roomsController->getAllRooms();
        $this->assertEquals($data['status'],200);
        $this->assertEquals($data['success'],'true');
        $this->assertEquals($data['collection'],'rooms');
        $this->assertEquals(count($data['rooms']),5944);
    }
    public function testHandleRequest_returns_room(){
        $data = $this->call('GET', 'api/1.0/rooms?room=' . $this->roomID);
        $content = json_decode($data->content(), 'true');
        $this->assertEquals($content['status'],200);
        $this->assertEquals($content['collection'],'rooms');
        $this->assertArrayHasKey('rooms',$content);
    }
    public function testHandleRequest_returns_all_rooms(){
        $data = $this->call('GET', 'api/1.0/rooms');
        $content = json_decode($data->content(), 'true');
        $this->assertEquals(count($content['rooms']),count($this->roomsController->getAllRooms()['rooms']));
        $this->assertEquals($content['status'],$this->roomsController->getAllRooms()['status']);
    }
    public function testHandleRequest_returns_error_array(){
        $data = $this->call('GET', 'api/1.0/rooms?invalid=invalid');
        $content = json_decode($data->content(), 'true');
        $this->assertEquals($content['status'],400);
        $this->assertEquals($content['success'],'false');
        $this->assertArrayHasKey('errors',$content);
    }
}