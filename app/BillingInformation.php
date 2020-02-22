<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class BillingInformation extends Model
{
    public $table = "billing_info";

    protected $fillable = [
        'billing_info_id', 
        'event_reservation_id', 
        'name',
        'phone',
        'email',
        'address',
        'discount_price',
        'tax_value',
        'tax_amount',
        'voucher_code',
        'total_amount',
        'currency',
        'payment_method',
        'payment_reference',
        'payment_date',
        'payment_status',
        'status',
        'created_date',
        'modified_date'
    ];
}
