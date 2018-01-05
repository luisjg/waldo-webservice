<?php

namespace App\Classes;

/**
 * This class exists to perform well-known calculations for the State Plane
 * Coordinate system. Its primary use is to convert X/Y relative coordinates
 * in a given zone to absolute latitude/longitude coordinates for mapping.
 */
class StatePlaneMapping
{
	/**
	 * Constant describing the conversion metric from kilometers to meters.
	 *
	 * @var float
	 */
	const KILOMETERS_TO_METERS = 1000.0;

	/**
	 * Constant describing the conversion metric from meters to kilometers.
	 *
	 * @var float
	 */
	const METERS_TO_KILOMETERS = 0.001;

	/**
	 * Constant describing the conversion metric from meters to feet.
	 *
	 * @var float
	 */
	const METERS_TO_FEET = 3.28084;

	/**
	 * Constant describing the conversion metric from feet to meters.
	 *
	 * @var float
	 */
	const FEET_TO_METERS = 0.3048;

	/**
	 * Constant describing the approximate radius of the Earth in kilometers.
	 * This is used in lat/long calculations due to the Earth's curviture.
	 *
	 * @var float
	 */
	const EARTH_RADIUS_KILOMETERS = 6371.0;

	/**
	 * Constant describing source units as feet for calculations.
	 *
	 * @var string
	 */
	const UNITS_FEET = "feet";

	/**
	 * Constant describing source units as meters for calculations.
	 *
	 * @var string
	 */
	const UNITS_METERS = "meters";

	/**
	 * Calculates and returns the the lat/long coordinates of a point based on
	 * the lat/long of the plate as well as the X distance (easting) and the Y
	 * distance (northing) from the plate.
	 * The return value is an array with a "lat" key and a "lon" key.
	 *
	 * @param float $plate_lat The latitude of the plate
	 * @param float $plate_lon The longitude of the plate
	 * @param float $dx_from_plate The distance away from the plate on the X axis
	 * @param float $dy_from_plate The distance away from the plate on the Y axis
	 * @param string $units Can be either "meters" or "feet" and represents the units
	 * used for the $dx_from_plate and $dy_from_plate parameters
	 * 
	 * @return array
	 */
	public static function findLatLongFromPlateDistance($plate_lat, $plate_lon,
		$dx_from_plate, $dy_from_plate, $units="meters") {
		// check the units and perform conversions if necessary
		if($units == self::UNITS_FEET) {
			$dx_from_plate = $dx_from_plate * self::FEET_TO_METERS;
			$dy_from_plate = $dy_from_plate * self::FEET_TO_METERS;
		}

		return self::latLongFromDistance(
			$plate_lat, $plate_lon, $dx_from_plate, $dy_from_plate
		);
	}

	/**
	 * Calculates and returns the origin of the plate based on a known
	 * latitude and longitude as well as an X distance (easting) and a Y
	 * distance (northing) from that plate.
	 * The return value is an array with a "lat" key and a "lon" key.
	 *
	 * @param float $known_lat The known latitude of a point
	 * @param float $known_lon The known longitude of a point
	 * @param float $dx_from_plate The distance to the point on the X axis from the plate
	 * @param float $dy_from_plate The distance to the point on the Y axis from the plate
	 * @param string $units Can be either "meters" or "feet" and represents the units
	 * used for the $dx_from_plate and $dy_from_plate parameters
	 * 
	 * @return array
	 */
	public static function findPlateOriginFromCoordDistance($known_lat, $known_lon,
		$dx_from_plate, $dy_from_plate, $units="meters") {
		
		// 1. Negate the distance from the plate so we can work backwards
		$dx_from_plate = -$dx_from_plate;
		$dy_from_plate = -$dy_from_plate;

		// 2. Perform any necessary unit conversions
		if($units == self::UNITS_FEET) {
			$dx_from_plate = $dx_from_plate * self::FEET_TO_METERS;
			$dy_from_plate = $dy_from_plate * self::FEET_TO_METERS;
		}

		// 3. Calculate the lat/long from our reversed distance
		return self::latLongFromDistance(
			$known_lat, $known_lon, $dx_from_plate, $dy_from_plate
		);
	}

	/**
	 * Calculates and returns a new lat/long from an existing lat/long as well
	 * as the Y distance (northing) from those coordinates.
	 * The return value is an array with a "lat" key and a "lon" key.
	 *
	 * This method only works in units of meters.
	 *
	 * @param float $lat The existing latitude to use for the calculation
	 * @param float $lon The existing longitude to use for the calculation
	 * @param float $northing The Y distance to add to the longitude
	 *
	 * @return array
	 *
	 * @see https://stackoverflow.com/a/7478827
	 *
	 * The longitude formula given in that StackOverflow answer is slightly
	 * wrong; dx should really be dy (northing) and latitude should really
	 * be the newly-calculated value of new_longitude.
	 */
	private static function latLongFromDistance($lat, $lon, $northing) {
		$new_lat = $lat + ($northing / (self::EARTH_RADIUS_KILOMETERS * self::KILOMETERS_TO_METERS)) *
			(180.0 / M_PI);

		$new_long = $lon + ($northing / (self::EARTH_RADIUS_KILOMETERS * self::KILOMETERS_TO_METERS)) *
			(180.0 / M_PI) /
			cos($new_lat * (M_PI/180.0));

		return [
			'lat' => $new_lat,
			'lon' => $new_long,
		];
	}
}