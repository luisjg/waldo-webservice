<?php

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
 * @param $headerArray
 * @return array
 */
function appendMessageDataToResponseHeader($headerArray, $message="success")
{
    return $headerArray += [
        'message' => $message
    ];
}

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
