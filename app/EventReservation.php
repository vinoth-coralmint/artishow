<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class EventReservation extends Model
{
    public $table = "event_reservation";

    protected $fillable = [
        'event_reservation_id',
        'reservation_id',
        'reservation_via',
        'first_name',
        'last_name',
        'gender',
        'company_name',
        'phone',
        'email',
        'fax',
        'address',
        'city',
        'postal_code',
        'event_addons',
        'promo_code',
        'reservation_notes',
        'total_number_of_person',
        'total_tax',
        'tax_amount',
        'total_discount',
        'remaining_amt',
        'pmt_amt',
        'reservation_total',
        'comment',
        'payment_status',
        'reservation_confirmed_status',
        'status',
        'reservation_date',
        'created_date',
        'modified_date'
    ];
}
