<?php
/*  Waldo Web Service - Backend that delivers CSUN room location information.
 *  Copyright (C) 2017-2019 - CSUN META+LAB
 *
 *  Waldo Web Service is free software: you can redistribute it and/or modify it under the terms
 *  of the GNU General Public License as published by the Free Software Found-
 *  ation, either version 3 of the License, or (at your option) any later version.
 *
 *  RetroArch is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 *  without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 *  PURPOSE.  See the GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License along with RetroArch.
 *  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    /**
     * Table name
     * @var string
     */
    protected $table = 'waldo.rooms';

    /**
     * Primary key
     * @var string
     */
    protected $primaryKey = 'code';

    /**
     * Not incrementing
     * @var bool
     */
    public $incrementing = false;

    /**
     * The values that can be written/updated
     * @var array
     */
    protected $fillable = [
        'latitude',
        'longitude'
    ];

    /**
     * Scope query that gets a room based on ID whether it's formatted
     * or not.
     * @param \ $query
     * @param string $roomId the unformatted room id
     * @param string $formattedRoomId the formatted room id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function scopeGetRoom($query,$roomId,$formattedRoomId){
        return $query->where('room', $roomId)
            ->orWhere('room', $formattedRoomId);
    }
}