<?php

namespace App\Http\Controllers;

use DB;
use App\Ical;
use Woocommerce;
use App\EventLineItem;
use App\EventReservation;
use App\BillingInformation;
use Illuminate\Http\Request;

class WooCommerceController extends Controller
{
    public function __construct() {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('woocommerce');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Ical  $ical
     * @return \Illuminate\Http\Response
     */
    public function show(Ical $ical)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Ical  $ical
     * @return \Illuminate\Http\Response
     */
    public function edit(Ical $ical)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Ical  $ical
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ical $ical)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Ical  $ical
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ical $ical)
    {
        //
    }

    /**
     * To sync the reservation details
     * return response
     */
    public function autoSync() {
        try{
            $lastInsertId = DB::table('event_reservation')->orderBy('event_reservation_id', 'desc')->first();
            if ($lastInsertId) {
                $data = [
                    'per_page'=> 100,
                    'page' => 1,
                    // 'before' => ($lastInsertId) ? $lastInsertId->reservation_date.'T00:00:00' : date('Y-m-d')."T00:00:00"
                    'after' => ($lastInsertId) ? $lastInsertId->reservation_date.'T00:00:00' : date('Y-m-d')."T00:00:00"
                ];
            } else {
                $data = [
                    'per_page'=> 100,
                    'page' => 1,
                ];
            }
            $orders_details = Woocommerce::get('orders', $data);  
            foreach($orders_details as $order) {
                $response  = $this->wooCommerceOrderDetails($order);
            }
        } catch (ModelNotFoundException $exception) {
            return back()->withError($exception->getMessage());
        }
        echo count($orders_details). ' of new records are successfully sync with tables' ;
    }

    /**
     * To update or create reservation details
     * @param $order
     */
    public function wooCommerceOrderDetails($order) {
        try {
            $last_insert_reservation_id = EventReservation::updateOrCreate(
                [
                    'first_name'  => $order['billing']['first_name'],
                    'last_name'   => $order['billing']['last_name'],
                    'gender'      => '',
                    'company_name'=> $order['billing']['company'],
                    'phone'       => $order['billing']['phone'],
                    'email'       => $order['billing']['email'],
                    'address'     => $order['billing']['address_1'].', ' . 
                                                    $order['billing']['address_2']. ', ' . 
                                                    $order['billing']['city']. ', ' . 
                                                    $order['billing']['state']. ', ' . 
                                                    $order['billing']['postcode']. ', ' . 
                                                    $order['billing']['postcode'],
                    'promo_code'            => '',
                    'reservation_notes'     => $order['customer_note'],
                    'tax_amount'            => $order['total'],
                    'total_tax'             => $order['total_tax'],
                    'total_discount'        => $order['discount_total'],
                    'reservation_confirmed_status' => $order['status'],
                    'reservation_date'      => date('Y-m-d', strtotime($order['date_created'])),
                    'reservation_via'       => 'online',
                    'reservation_id'        => $order['id']
                ],
                [
                    'status'                => 1
                ]
            );
            $last_inserted_reservation = $last_insert_reservation_id->event_reservation_id;
            $event_reservation = $this->eventRegistration($order, $last_inserted_reservation);
            $billing_information = $this->billingInformation($order, $last_inserted_reservation);

        } catch(ModelNotFoundException $exception) {
            return back()->withError($exception->getMessage());
        }
        
    }

    /**
     * To update or create event reservation details
     * @param $order, $last_inserted_reservation
     */
    public function eventRegistration($order, $last_inserted_reservation) {
        try {
            foreach($order['line_items'] as $event) {
                EventLineItem::updateOrCreate([
                    'event_reservation_id'  => $last_inserted_reservation,
                    'event_name'            => ($event['meta']) ? $event['meta'][0]['value'] : '',
                    'event_categories'      => $event['name'],
                    'number_of_person'      => '',
                    'price_per_person'      => $event['price'],
                    'total_price'           => $event['total']],
                    [ 
                        'event_quantity'    => '100', 
                        'event_categories'  => 0  
                    ]
                );
            }
        } catch(ModelNotFoundException $exception) {
            return back()->withError($exception->getMessage());
        }
    }

    /**
     *  To update or create billing details
     * @param $order, $last_inserted_reservation
     */
    public function billingInformation($order, $last_inserted_reservation) {
        try {
            BillingInformation::updateOrCreate([
                'event_reservation_id'    => $last_inserted_reservation,
                'payment_method'          => $order['payment_method'],
                'payment_reference'       => $order['transaction_id'],
                'payment_date'            => date('Y-m-d', strtotime($order['date_paid']))    
            ],
                ['status'                 => 0 ]
            );
        } catch(ModelNotFoundException $exception) {
            return back()->withError($exception->getMessage());
        }
    }
}
