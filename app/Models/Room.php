<?php

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