<?php

use App\Classes\StatePlaneMapping;
use App\Http\Controllers\RoomsController;
use Illuminate\Support\Str;

class RoomsControllerTest extends TestCase
{
    protected $roomsController;
    protected $roomID;
    protected $fakeID;
    protected $lat;
    protected $lon;
    protected $x;
    protected $y;

    public function setUp()
    {
        parent::setUp();
        $this->roomsController = new RoomsController;
        $this->fakeID = 'JD99999';
        $this->roomID = 'JD2211';
        $this->lat = '34.241411449';
        $this->lon = '-118.529299946';
        $this->x = '6401698.512';
        $this->y = '1910657.49';
    }

    public function testJsonHeader_returns_header()
    {
        $data = $this->roomsController->getRoom($this->roomID);
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('api', $data);
        $this->assertArrayHasKey('version', $data);
        $this->assertArrayHasKey('collection', $data);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('rooms', $data);
    }

    public function testGetRoom_returns_room()
    {
        $data = $this->roomsController->getRoom($this->roomID);
        $this->assertEquals($data['status'],200);
        $this->assertEquals($data['success'],'true');
        $this->assertEquals($data['collection'],'rooms');
        $this->assertEquals($data['count'],1);
        $this->assertArrayHasKey('room_number', $data['rooms'][0]);
        $this->assertArrayHasKey('building_name', $data['rooms'][0]);
        $this->assertArrayHasKey('latitude', $data['rooms'][0]);
        $this->assertArrayHasKey('longitude', $data['rooms'][0]);
        $this->assertEquals($data['rooms'][0]['room_number'],$this->roomID);
        $this->assertEquals($data['rooms'][0]['building_name'],'Jacaranda Hall');
        $this->assertEquals($data['rooms'][0]['latitude'],$this->lat);
        $this->assertEquals($data['rooms'][0]['longitude'],$this->lon);
    }

    public function testGetRoom_returns_error()
    {
        $data = $this->roomsController->getRoom($this->fakeID);
        $this->assertEquals($data['status'],404);
        $this->assertEquals($data['success'],'false');
        $this->assertArrayHasKey('errors',$data);
    }

    public function testGetAllRooms_returns_all_rooms()
    {
        $data = $this->roomsController->getAllRooms();
        $this->assertEquals($data['status'],200);
        $this->assertEquals($data['success'],'true');
        $this->assertEquals($data['collection'],'rooms');
        $this->assertEquals(count($data['rooms']), $data['count']);
        $this->assertArrayHasKey('room_number', $data['rooms'][0]);
        $this->assertArrayHasKey('building_name', $data['rooms'][0]);
        $this->assertArrayHasKey('latitude', $data['rooms'][0]);
        $this->assertArrayHasKey('longitude', $data['rooms'][0]);
    }

    public function testHandleRequest_returns_room()
    {
        $data = $this->call('GET', 'api/1.0/rooms?room=' . $this->roomID);
        $content = json_decode($data->content(), 'true');
        $this->assertEquals($content['status'],200);
        $this->assertEquals($content['collection'],'rooms');
        $this->assertArrayHasKey('rooms',$content);
    }

    public function testHandleRequest_returns_all_rooms()
    {
        $data = $this->call('GET', 'api/1.0/rooms');
        $content = json_decode($data->content(), 'true');
        $this->assertEquals(count($content['rooms']),count($this->roomsController->getAllRooms()['rooms']));
        $this->assertEquals($content['status'],$this->roomsController->getAllRooms()['status']);
    }

    public function testHandleRequest_returns_error_array()
    {
        $data = $this->call('GET', 'api/1.0/rooms?invalid=invalid');
        $content = json_decode($data->content(), 'true');
        $this->assertEquals($content['status'],400);
        $this->assertEquals($content['success'],'false');
        $this->assertArrayHasKey('errors',$content);
    }

    public function testCheckConversion_returns_true_false()
    {
        $data = $this->roomsController->getRoom($this->roomID);
        $map = new StatePlaneMapping();
        $result = $map->convertPointToLatLong(	
            $this->x,	
            $this->y
        );
        $this->assertEquals($data['rooms'][0]['latitude'], $result['lat']);
        $this->assertEquals($data['rooms'][0]['longitude'], $result['lon']);
    }
}