<?php

function buildResponseHeader($collection, $success, $status_code, $version)
{
    return $response = [
            'success'    => ($success ? "true" : "false"),
            'status'     => strval($status_code),
            'api'        => 'Waldo',
            'version'    => number_format($version,1),
            'collection' => $collection
    ];
}

function appendResponseWithRoomInfo($response, $roomData)
{
        return $response += [
            'count' => count($roomData),
            'rooms' => $roomData
        ];
}

