<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductDetails extends Model
{
    public $table = 'property';

    protected $visible = ['property_id', 
    'owner_id', 
    'property_name', 
    'room_type', 
    'room_count', 
    'property_doorno', 
    'property_street_name', 
    'property_postalcode',
    'property_city',
    'property_country',
    'property_floor',
    'property_type',
    'property_age',
    'property_state',
    'property_area_spft',
    'total_area_of_property',
    'percentage',
    'access_code',
    'agreement_end_date',
    'ical_link',
    'status',
    'create_at',
    'modified_at'
];


}
