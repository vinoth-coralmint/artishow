<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class EventLineItem extends Model
{
    public $table = "event_line_items";

    protected $fillable = [
        'event_line_item_id', 
        'event_reservation_id', 
        'event_formulae_id',
        'event_id',
        'event_name',
        'event_description',
        'event_categories',
        'number_of_person',
        'event_quantity',
        'show_date',
        'event_start_date',
        'event_end_date',
        'event_service_time',
        'price_per_person',
        'discount_price',
        'total_price',
        'created_date',
        'modified_date'
    ];
}
