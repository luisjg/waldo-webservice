<?php

namespace App\Classes;

class CoordinatesHelper
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
}