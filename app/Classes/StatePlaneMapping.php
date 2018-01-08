<?php

namespace App\Classes;

use proj4php\Proj4php;
use proj4php\Proj;
use proj4php\Point;

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
	 * The instance of Proj4 for mapping and transformation.
	 *
	 * @var Proj4php
	 */
	private $proj4;

	/**
	 * Constructs a new StatePlaneMapping instance.
	 */
	public function __construct() {
		// Initialise Proj4
		// https://github.com/proj4php/proj4php
		$this->proj4 = new Proj4php();

		// initialize zone 0405 (California Zone 5) SPC datum by default since
		// this is a mapping application for a specific part of Los Angeles
		// http://spatialreference.org/ref/epsg/2229 (select Proj4 format)
		$this->addDatumDefinition("EPSG:2229",
			"+proj=lcc +lat_1=35.46666666666667 +lat_2=34.03333333333333 +lat_0=33.5 +lon_0=-118 +x_0=2000000.0001016 +y_0=500000.0001016001 +ellps=GRS80 +datum=NAD83 +to_meter=0.3048006096012192 +no_defs"
		);
	}

	/**
	 * Adds a datum definition in Proj4 format to this mapping class.
	 *
	 * @param string $datum The name of the datum being added (ex: EPSG:2229)
	 * @param string $proj4Format The Proj4 format of the datum being added
	 *
	 * @see http://spatialreference.org/ref/epsg/
	 */
	public function addDatumDefinition($datum, $proj4Format) {
		$this->proj4->addDef($datum, $proj4Format);
	}

	/**
	 * Converts an X/Y point to lat/long coordinates. Returns an array with the
	 * keys "lat" and "lon" after the conversion.
	 *
	 * @param float $x The X coordinate of the point
	 * @param float $y The Y coordinate of the point
	 * @param string $datum Optional datum format of point. Default is EPSG:2229
	 *
	 * @return array
	 */
	public function convertPointToLatLong($x, $y, $datum="EPSG:2229") {
		// initialize the projection with the desired datum
		$projection = new Proj($datum, $this->proj4);

		// initialize the lat/long projection (EPSG:4326)
		$projWGS84  = new Proj('EPSG:4326', $this->proj4);

		// generate a point based on the X/Y coordinates within the desired
		// projection initialized above
		$pointSrc = new Point($x, $y, $projection);

		// transform the datum to the new format
		$pointDest = $this->proj4->transform($projWGS84, $pointSrc);

		// return an array containing the lat/long values; the values are
		// rounded to 9 digits of precision to match Facilities values
		return [
			'lat' => round($pointDest->y, 9, PHP_ROUND_HALF_UP),
			'lon' => round($pointDest->x, 9, PHP_ROUND_HALF_UP),
		];
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
	 * @param string $type "point" to calculate regular point; "plate" to calculate
	 * the plate coordinates
	 *
	 * @return array
	 *
	 * @see https://stackoverflow.com/a/7478827
	 *
	 * The longitude formula given in that StackOverflow answer is slightly
	 * wrong; dx should really be dy (northing) and latitude should really
	 * be the newly-calculated value of new_longitude. In addition, the calculation
	 * of the longitude needs to be done differently based upon whether we are
	 * working backwards to calculate the plate coordinates from a known position
	 * or using the plate coordinates to calculate unknown coordinates using a
	 * distance on the Y axis.
	 */
	public static function latLongFromDistance($lat, $lon, $northing, $type="point") {
		$new_lat = $lat + ($northing / (self::EARTH_RADIUS_KILOMETERS * self::KILOMETERS_TO_METERS)) *
			(180.0 / M_PI);

		if($type == "point") {
			// non-plate calculation needs the original latitude parameter
			$latval = $lat;
		}
		else
		{
			// plate calculation needs the newly-calculated latitude
			$latval = $new_lat;
		}

		$new_long = $lon + ($northing / (self::EARTH_RADIUS_KILOMETERS * self::KILOMETERS_TO_METERS)) *
			(180.0 / M_PI) /
			cos($latval * (M_PI/180.0));

		return [
			'lat' => $new_lat,
			'lon' => $new_long,
		];
	}
}