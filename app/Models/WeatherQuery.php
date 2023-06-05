<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherQuery extends Model
{
    use HasFactory;

    protected $primaryKey = 'query_id';
    protected $fillable = [
        'service_id',
        'date',
        'ip_address',
        'latitude',
        'longitude',
    ];
}
