<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cafe extends Model
{
    protected $fillable = ['name', 'address', 'city', 'state', 'zip', 'latitude', 'longitude'];
}
