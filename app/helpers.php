<?php
/**
 * @param $statusCode
 * @param $successBool
 * @return array
 */
function buildResponseHeaderArray($statusCode, $successBool)
{
    return $response =[
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
        'collection'    => $collectionName,
        'count'         => strval(count($collection)),
        $collectionName => $collection
    ];
}

/**
 * @param $headerArray
 * @return array
 */
function appendErrorDataToResponseHeader($headerArray){
    $errors = ['An error has occurred'];
    return $headerArray += [
        'errors' => $errors
    ];

}
