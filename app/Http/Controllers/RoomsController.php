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
            $status = $formattedData->status;
            $process = new Process('php ../artisan update:room all > /dev/null &');
            $process->start();
        } else {
            $formattedData = formatAllRoomsCollection();
            $status = $formattedData['status'];
        }
        return $this->sendResponse($formattedData, $status);
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
            return $this->sendResponse($formattedResponse, $formattedResponse->status);
        } else {
            $formattedResponse = formatRoomCollection($roomId);
            if ($formattedResponse) {
                return $formattedResponse;
            } else {
                $response = buildResponseHeaderArray(404, 'false');
                $formattedResponse = appendErrorDataToResponseHeader($response);
                return $this->sendResponse($formattedResponse, $formattedResponse->status);
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
        return $this->sendResponse($formattedResponse, $formattedResponse->status);
    }
}
