<?php

function buildResponseArray($collection, $success, $status_code, $version)
{
    return $response = [
            'success' => strval($success),
            'status' => strval($status_code),
            'api' => 'Waldo',
            'version' => strval($version)
    ];
}

