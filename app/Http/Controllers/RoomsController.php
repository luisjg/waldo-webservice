<?php

namespace App\Http\Controllers;

use App\Classes\StatePlaneMapping;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomsController extends Controller
{

    /**
     * Retrieves the landing page
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
        if(is_null($request->getQueryString())){
            return $this->getAllRooms();
        }else if($request->has('room')){
            return $this->getRoom($request->get('room'));
        } else {
            $header = buildResponseHeaderArray(400, 'false');
            return appendErrorDataToResponseHeader($header);
        }
    }

    /**
     * Retrieves all the rooms from the database
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAllRooms()
    {
        $allRooms = Room::all();
        $allRooms = $allRooms->map(function($roomDetail) {
            return [
                'room_number'   => $roomDetail->room,
                'building_name' => $roomDetail->building_name,
                'latitude'      => $roomDetail->latitude,
                'longitude'     => $roomDetail->longitude
                ];
        });
        $header = buildResponseHeaderArray(200, 'true');
        return appendRoomDataToResponseHeader($header, 'rooms', $allRooms);
    }

    /**
     * Retrieves the specific rooms information
     * @param string $roomId the room ID
     * @return array the JSON array
     * @internal param Request $request the request URI
     */
    public function getRoom($roomId)
    {
        $formattedRoomId = $this->formatRoomNumber($roomId);
        $room = Room::getRoom($roomId,$formattedRoomId)->first();
        $response = buildResponseHeaderArray($room == null ? 404 : 200,$room == null ? 'false' : 'true');
        if($room != null){
            if($room->longitude != null && $room->latitude != null) {
                $roomsCollection[] = [
                    'room_number'   => $room->room,
                    'building_name' => $room->building_name,
                    'latitude'      => $room->latitude,
                    'longitude'     => $room->longitude
                ];
                return appendRoomDataToResponseHeader($response, 'rooms', $roomsCollection);
            } else {
                // retrieve the point mapped as lat/long and save it to the
                // database
                $map = new StatePlaneMapping();
                $point = $map->convertPointToLatLong
                    ($room->x_coordinate, $room->y_coordinate);
                $room->update([
                    'longitude' => $point['lon'],
                    'latitude'  => $point['lat'],
                ]);
                $room->touch();
                $room->save();

                $roomsCollection[] = [
                    'room_number'   => $room->room,
                    'building_name' => $room->building_name,
                    'latitude'      => $room->latitude,
                    'longitude'     => $room->longitude
                ];
                return appendRoomDataToResponseHeader($response, 'rooms', $roomsCollection);
            }
        } else {
            return appendErrorDataToResponseHeader($response);
        }
    }

    /**
     * Calculates all missing lat/long values for rooms in the database and
     * updates the records.
     *
     * @return array the JSON array
     */
    public function syncRoomCoordinates() {
        $rooms = Room::whereNull('latitude')
            ->whereNull('longitude')
            ->get();
        if($rooms->count() > 0) {
            $map = new StatePlaneMapping();
            foreach($rooms as $room) {
                $point = $map->convertPointToLatLong
                    ($room->x_coordinate, $room->y_coordinate);
                $room->update([
                    'longitude' => $point['lon'],
                    'latitude'  => $point['lat'],
                ]);
                $room->touch();
                $room->save();
            }
            $message = $rooms->count() . " room(s) updated";
        }
        else
        {
            $message = "0 rooms updated";
        }

        $response = buildResponseHeaderArray(200, "true");
        return appendMessageDataToResponseHeader($response, $message);
    }

    /**
     * Prepends 1 or more zeroes to a number.
     * @param string|int $num - The number to which you want to prepend zeroes
     * @param int $count - (optional) How many zeroes you would like to prepend. Defaults to 1.
     * @return int|string
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
