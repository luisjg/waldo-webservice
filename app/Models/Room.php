<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'waldo.rooms';
    protected $primaryKey = 'code';
    public $incrementing = false;

    public function scopeGetRoom($query,$roomId,$formattedRoomId){
        return $query->where('room', $roomId)
            ->orWhere('room', $formattedRoomId)
            ->first();
    }
}