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
                'status'    => '400',
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
            'success'     => 'true',
            'collection'  => 'rooms',
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
        $formattedRoomId = $this->formatRoomNumber($roomId);
        $room = Room::where('room', $roomId)
                    ->orWhere('room', $formattedRoomId)
                    ->first();
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
                        'status'    => '400',
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

    /*
     * Prepends 1 or more zeroes to a number.
     * @param string|int $num - The number to which you want to prepend zeroes
     * @param int $count    - (optional) How many zeroes you would like to prepend. Defaults to 1.
     */
    private function prependZeroes($num, $count=1) {
        for ($i = 0; $i < $count; $i++) {
            $num = '0' . $num;
        }
        return $num;
    }

    /**
     * For some rooms like SH 173, they are stored in the database with 4-digit numbers by prepending zeros.
     * However, some rooms  like UV00D8 are stared as 2-digits. Keep that in mind when using this method to help query
     * the database. This also removes whitespace in the name.
     * @param $room - Un-formatted Room with  less than 4 digits (ex:  "SH 175").
     * @return mixed|string  - The formatted room string with 4 digits and no spaces (ex: "SH0175") ready for querying.
     */
    private function formatRoomNumber($room) {
        // Remove whitespace and case.
        $room = preg_replace('/\s/','',strtoupper($room));

        // The number of digits a room has, according to the database.
        $desiredNumberOfDigits = 4;

        // This is the regex to which all room numbers should conform before hitting the database.
        // The x flag allows comments in regex.
        $desiredFormat = "/
                            [A-Z]+                               # This matches the 2-letter Building code.
                            [0-9]{1,$desiredNumberOfDigits}       # This matches the number part.
                            (                                    # This group matches the optional alphanumeric suffix.
                                [A-Z]                            # It must start with a letter to be distinguishable.
                                [A-Z0-9]*                        # May contain a mix of numbers and letters.
                            )?                                   # Let it be optional.
                         /x";

        // A callback to a regex that matches a string of digits.
        $insertNeededZeroesCallback = function($matches) use ($desiredNumberOfDigits) {
            //Match holds the 2210 in JD2210
            $match = $matches[0];
            $numberOfNeededZeroes = $desiredNumberOfDigits - strlen($match);
            return $this->prependZeroes($match, $numberOfNeededZeroes);
        };

        // A callback to a regex that matches a valid room format.
        $ensureProperFormatCallback = function($matches) use($desiredNumberOfDigits, $insertNeededZeroesCallback) {
            $match = $matches[0];
            $replacement = preg_replace_callback(
                "/[0-9]{1,$desiredNumberOfDigits}/",
                $insertNeededZeroesCallback,
                $match
            );
            return $replacement;
        };

        // Perform the actual formatting of the string.
        $room = preg_replace_callback(
            $desiredFormat,
            $ensureProperFormatCallback,
            $room
        );
        return $room;
    }
}
