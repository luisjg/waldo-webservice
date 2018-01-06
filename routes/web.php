<?php

use App\Classes\StatePlaneMapping;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', 'RoomsController@index');

$app->get('/about/version-history', function() {
    return view('pages.about.version-history');
});

$app->group(['prefix' => 'api/1.0'], function() use ($app) {
    $app->get('/rooms', 'RoomsController@handleRequest');
});

$app->get('calc', function() {
	$map = new StatePlaneMapping();
	$result = $map->convertPointToLatLong(
		6401894.55600000,
		1910627.60100000
	); // X/Y for JD1600A (slightly more precise than Facilities)
	dd($result);
});