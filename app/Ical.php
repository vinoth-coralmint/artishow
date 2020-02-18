<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Ical extends Model
{
    public $table = "occupancy_info";

    protected $visible = [
        'occupancy_id', 
        'property_id', 
        'date',
        'property_id',
        'rent',
        'occupancy_status',
        'status',
        'created_at',
        'modified_at'
    ];
}
