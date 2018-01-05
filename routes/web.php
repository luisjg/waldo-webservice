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
	// known coordinates of MZ0000
	$known_lat = 34.237628419;
	$known_long = -118.530378707;

	// plate coordinates (as of Jan 5, 2018)
	$plate_lat = 29.0040325363;
	$plate_long = -124.51446532;

	$dx = 6401365.22100000; // negate for known -> plate
	$dy = 1909282.51400000; // negate for known -> plate

	$plate_coords = StatePlaneMapping::findPlateOriginFromCoordDistance(
		$known_lat, $known_long, $dx, $dy, StatePlaneMapping::UNITS_FEET
	);
	$bldg_coords = StatePlaneMapping::findLatLongFromPlateDistance(
		$plate_coords['lat'], $plate_coords['lon'], $dx, $dy, StatePlaneMapping::UNITS_FEET
	);
	dd($plate_coords, $bldg_coords);
});