<?php

function buildResponseArray($collectionName, $collection)
{
    return $response = [
            'status'        => '200',
            'success'       => 'true',
            'collection'    => $collectionName,
            $collectionName => $collection
    ];
}

