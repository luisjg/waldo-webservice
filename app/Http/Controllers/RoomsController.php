<?php

namespace App\Http\Controllers;

use App\Classes\StatePlaneMapping;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

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
     *
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
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRooms()
    {
        if (File::exists(storage_path('room/all-rooms.txt'))) {
            $formattedData = File::get(storage_path('room/all-rooms.txt'));
            $formattedData = json_decode($formattedData);
            $process = new Process('php ../artisan update:room all > /dev/null &');
            $process->start();
        } else {
            $formattedData = formatAllRoomsCollection();
        }
        return $this->sendResponse($formattedData);
    }

    /**
     * Retrieves the specific rooms information
     *
     * @param string $roomId the room ID
     * @return \Illuminate\Http\JsonResponse
     * @internal param Request $request the request URI
     */
    public function getRoom($roomId)
    {
        if (File::exists(storage_path('room/'.$roomId.'.txt'))) {
            $formattedResponse = File::get(storage_path('room/'.$roomId.'.txt'));
            $formattedResponse = json_decode($formattedResponse);
            $process = new Process('php ../artisan update:room ' . $roomId . ' > /dev/null &');
            $process->start();
            return $this->sendResponse($formattedResponse);
        } else {
            $formattedResponse = formatRoomCollection($roomId);
            if ($formattedResponse) {
                return $formattedResponse;
            } else {
                $response = buildResponseHeaderArray(404, 'false');
                $formattedResponse = appendErrorDataToResponseHeader($response);
                return $this->sendResponse($formattedResponse);
            }
        }
    }

    /**
     * Calculates all missing lat/long values for rooms in the database and
     * updates the records.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncRoomCoordinates() {
        $rooms = Room::whereNull('latitude')
            ->whereNull('longitude')
            ->get();
        if ($rooms->count() > 0) {
            $map = new StatePlaneMapping();
            foreach ($rooms as $room) {
                $point = $map->convertPointToLatLong
                    ($room->x_coordinate, $room->y_coordinate);
                $room->update([
                    'longitude' => $point['lon'],
                    'latitude' => $point['lat'],
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
        $formattedResponse = appendMessageDataToResponseHeader($response, $message);
        return $this->sendResponse($formattedResponse);
    }
}
