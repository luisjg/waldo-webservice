<?php
/*  Waldo Web Service - Backend that delivers CSUN room location information.
 *  Copyright (C) 2017-2019 - CSUN META+LAB
 *
 *  Waldo Web Service is free software: you can redistribute it and/or modify it under the terms
 *  of the GNU General Public License as published by the Free Software Found-
 *  ation, either version 3 of the License, or (at your option) any later version.
 *
 *  RetroArch is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 *  without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 *  PURPOSE.  See the GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License along with RetroArch.
 *  If not, see <http://www.gnu.org/licenses/>.
 */

use App\Classes\StatePlaneMapping;
use App\Models\Room;
use Illuminate\Support\Facades\File;

/**
 * @param $statusCode
 * @param $successBool
 * @return array
 */
function buildResponseHeaderArray($statusCode, $successBool)
{
    return $response = [
        'success' => $successBool,
        'status' => $statusCode,
        'api' => 'waldo',
        'version' => '1.0'
    ];
}

/**
 * @param $headerArray
 * @param $collectionName
 * @param $collection
 * @return array
 */
function appendRoomDataToResponseHeader($headerArray, $collectionName, $collection)
{
    return $headerArray += [
        'collection' => $collectionName,
        'count' => strval(count($collection)),
        $collectionName => $collection
    ];
}

/**
 * @param $headerArray
 * @return array
 */
function appendErrorDataToResponseHeader($headerArray)
{
    $errors = ['An error has occurred'];
    return $headerArray += [
        'errors' => $errors
    ];

}

/**
 * Appends a message to an array
 *
 * @param $headerArray
 * @return array
 */
function appendMessageDataToResponseHeader($headerArray, $message="success")
{
    return $headerArray += [
        'message' => $message
    ];
}

/**
 * Formats the all rooms data and stores it in the cache
 *
 * @return array
 */
function formatAllRoomsCollection()
{
    $allRooms = Room::all();
    $allRooms = $allRooms->map(function($roomDetail) {
        return [
            'room_number' => $roomDetail->room,
            'building_name' => $roomDetail->building_name,
            'latitude' => $roomDetail->latitude,
            'longitude' => $roomDetail->longitude
        ];
    });
    $header = buildResponseHeaderArray(200, 'true');
    $formattedData = appendRoomDataToResponseHeader($header, 'rooms', $allRooms);
    storeRoomInLocalCache('all-rooms', $formattedData);
    return $formattedData;
}


/**
 * Formats a specific room's collection and saves it to the cache
 *
 * @param $roomId
 * @return array
 */
function formatRoomCollection($roomId)
{
    $formattedRoomId = formatRoomNumberHelper($roomId);
    $room = Room::getRoom($roomId, $formattedRoomId)->first();
    $response = buildResponseHeaderArray($room == null ? 404 : 200, $room == null ? 'false' : 'true');
    if ($room != null) {
        if ($room->longitude != null && $room->latitude != null) {
            $roomsCollection[] = [
                'room_number' => $room->room,
                'building_name' => $room->building_name,
                'latitude' => $room->latitude,
                'longitude' => $room->longitude
            ];
            $formattedResponse = appendRoomDataToResponseHeader($response, 'rooms', $roomsCollection);
            storeRoomInLocalCache($roomId, $formattedResponse);
            return $formattedResponse;
        } else {
            // retrieve the point mapped as lat/long and save it to the
            // database
            $map = new StatePlaneMapping();
            $point = $map->convertPointToLatLong
            ($room->x_coordinate, $room->y_coordinate);
            $room->update([
                'longitude' => $point['lon'],
                'latitude' => $point['lat'],
            ]);
            $room->touch();
            $room->save();

            $roomsCollection[] = [
                'room_number' => $room->room,
                'building_name' => $room->building_name,
                'latitude' => $room->latitude,
                'longitude' => $room->longitude
            ];
            $formattedResponse = appendRoomDataToResponseHeader($response, 'rooms', $roomsCollection);
            storeRoomInLocalCache($roomId, $formattedResponse);
            return $formattedResponse;
        }
    }
}

/**
 * Stores room data in the local cache
 *
 * @param $room
 * @param $data
 */
function storeRoomInLocalCache($room, $data)
{
    if (!File::exists(storage_path('room'))) {
        File::makeDirectory(storage_path('room'));
    }
    $data = json_encode($data);
    File::put(storage_path('room/'.$room.'.txt'), $data);
}

/**
 * For some rooms like SH 173, they are stored in the database with 4-digit numbers by prepending zeros.
 * However, some rooms  like UV00D8 are stared as 2-digits. Keep that in mind when using this method to help query
 * the database. This also removes whitespace in the name.
 * @param $room - Un-formatted Room with  less than 4 digits (ex:  "SH 175").
 * @return mixed|string  - The formatted room string with 4 digits and no spaces (ex: "SH0175") ready for querying.
 */
function formatRoomNumberHelper($room)
{
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
        return prependZeroes($match, $numberOfNeededZeroes);
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

/**
 * Prepends 1 or more zeroes to a number.
 * @param string|int $num - The number to which you want to prepend zeroes
 * @param int $count - (optional) How many zeroes you would like to prepend. Defaults to 1.
 * @return int|string
 */
function prependZeroes($num, $count=1)
{
    for ($i = 0; $i < $count; $i++) {
        $num = '0' . $num;
    }
    return $num;
}
