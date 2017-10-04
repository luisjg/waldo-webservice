<?php
/**
 * @param $statusCode
 * @param $successBool
 * @return array
 */
function buildResponseHeaderArray($statusCode, $successBool)
{
    return $response =[
        'status' => strval($statusCode),
        'success' => ($successBool ? 'true' : 'false'),
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
        $collectionName => $collection
    ];
}

/**
 * @param $headerArray
 * @return array
 */
function appendErrorDataToResponseHeader($headerArray){
    return $headerArray += [
        'errors' => 'An error has occured'
    ];
}
