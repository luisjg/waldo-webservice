<?php

function buildRoomHeaderArray()
{
    return $response = [
            'success'    => ($success ? "true" : "false"),
            'status'     => strval($status_code),
            'collection' => $collection
    ];
}

