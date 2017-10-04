<?php

function buildHeaderArray($collection)
{
    return $response = [
            'status'      => '200',
            'success'     => 'true',
            'collection'  => $collection,
    ];
}

