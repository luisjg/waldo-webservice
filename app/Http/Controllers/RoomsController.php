<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomsController extends Controller
{

    /**
     * Retrieves the landing page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('pages.index');
    }

    /**
     * Handles the request if any and returns relevant JSON
     * @param Request $request
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function handleRequest(Request $request)
    {
        if($request->has('room')) {
            return $this->getRoom($request->get('room'));
        } else if(is_null($request->getQueryString())) {
            return $this->getAllRooms();
        } else {
            return array(
                'status'    => '200',
                'success'   => 'false',
                'errors'    => array(
                    'message'	=> 'An error occurred'
                )
            );
        }
    }

    /**
     * Retrieves all the rooms from the database
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAllRooms()
    {
        $rooms = Room::all();
        return array(
            'status'      => '200',
            'success'     => 'false',
            'collection'  => 'rooms',
//            'count'       => count($rooms),
            'rooms'       => $rooms
        );
    }

    /**
     * Retrieves the specific rooms information
     * @param Request $request the request URI
     * @return array
     */
    public function getRoom($roomId)
    {
        $room = Room::where('room', $roomId)->first();
        if($room != null){
            if($room->longitude != null){
                $lon = $room->longitude;
                $lat = $room->latitude;
            }
            else{
                $client = new \GuzzleHttp\Client();
                $roomX = $room->x_coordinate;
                $roomY = $room->y_coordinate;
                // This is a fallback
                try{
                    //   http://beta.ngs.noaa.gov/gtkweb/
                    //   http://beta.ngs.noaa.gov/gtkws/geo?northing=76470.584 &easting=407886.482&zone=3702

                    // new url
                    $request = $client->get(env('GIS_WEB_SERVICE_URL') . "/spc?spcZone=0405&inDatum=nad83(NSRS2007)&outDatum=nad83(2011)&northing=" . $roomY . "&easting=" . $roomX . "&zone=0405 &units=usft");

                    $point = $request->json();
                    $lon = $point['lon'];
                    $lat = $point['lat'];

                    $room->update([
                        'longitude' => $lon,
                        'latitude' => $lat
                    ]);
                    $room->touch();
                    $room->save();
                }catch(\GuzzleHttp\Exception\RequestException $e){
                    return array(
                        'status'    => '200',
                        'success'   => 'false',
                        'errors'    => array(
                            'message'	=> 'An error occurred'
                        )
                    );
                }

            }
        }
        $response = array(
            'status'		  => $room == null ? '404' : '200',
            'success'		  => $room == null ? 'false' : 'true',
            'collection'      => 'room',
//            'count'           => $room->count(),
            'room'			  => $room == null ? array() : array(
                'room_number'	  => $room->room,
                'building_name'	  => $room->building_name,
                'latitude'        => $lat,
                'longitude'		  => $lon
            )
        );

        return $response;
    }
}
