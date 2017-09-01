<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'waldo.rooms';
    protected $primaryKey = 'code';
    public $incrementing = false;
}