<?php

namespace App\Http\Controllers;

use DB;
use App\Ical;
use App\ProductDetails;
use Illuminate\Http\Request;
// use App\Libraries\ical\zapcallib;

class IcalController extends Controller
{
    public function __construct() {
        require app_path().'/Libraries/ical/zapcallib.php';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('ical');
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
     * To auto sync the calender booking details
     * return boolean
     */
    public function autoSync() {
        $properties = ProductDetails::where('status', 1)->get(); // Active = 1
        try{
            foreach($properties as $row) {
                // echo "<pre>";
                // print_r($row->property_id);
                // print_r($row->ical_link);
                $test = $this->icalFormatter($row);
                //dd($test, 'final');

            } 
        } catch (ModelNotFoundException $exception) {
            return back()->withError($exception->getMessage());
        }
        
        echo 'Successfully Sync with tables'; exit;
        // while($row = $properties) {
        //     echo '<pre>';print_r($row);
        // }exit;
        // dd($properties);
    }

    public function icalFormatter($property) {

        $icalObj = new \ZCiCal();
        $eventobj = new \ZCiCalNode("VEVENT ", $icalobj->curnode);

        // Define default
        $icalstring = file_get_contents($property->ical_link);
        $icalobj = null;
        $eventcount = 0;
        $maxevents = 500;

        try {
    	    $icalobj = new \ZCiCal($icalstring, $maxevents, $eventcount);
    	}
    	catch(Exception $e){
    	    print_r("Exception".$e);
        }
        $eventcount +=$maxevents;

    foreach($icalobj->tree->child as $node)
    {
        $parentNode = $node->getParent();
        if($parentNode->getName()=="VCALENDAR")
        {
            foreach($parentNode->data as $key => $value)
            {
                if($key == "X-MA-PROPERTY-ID" || $key == "X-MA-ROOM-ID" || $key == "X-MA-ROOM-LABEL")
                {
                    print_r("Key".$key."  ".$value->getValues());
                   
                }
            }
        }

    	if($node->getName() == "VEVENT")
    	{
    	    $startdate=date('Y-m-d');
    	    $enddate=date('Y-m-d');
    	    $roomid="";
    		foreach($node->data as $key => $value)
    		{
    			if($key == "SUMMARY")
    			{
    			    $roomid=$value->getValues();
    				foreach($value->getParameters() as $key1 => $value1)
    				{
    				    print_r("event parameter".$value1);
    				}
    			}
    			else if($key == "DTSTART")
    			{
    			    $startdate=$value->getValues();
    			    $startdate = str_replace('T', '', $startdate);//remove T
                    $startdate = str_replace('Z', '', $startdate);//remove Z
                    $d    = date('d', strtotime($startdate));//get date day
                    $m    = date('m', strtotime($startdate));//get date month
                    $y    = date('Y', strtotime($startdate));//get date year
                    //$now = date('Y-m-d G:i:s');//current date and time
                    $startdate = date('Y-m-d', strtotime($startdate));//user friendly date
    			    //echo "event start date: ".$startdate;
    			       
    			}
    			else if($key == "DTEND")
    			{
    			    $enddate=$value->getValues();
    			    $enddate = str_replace('T', '', $enddate);//remove T
                    $enddate = str_replace('Z', '', $enddate);//remove Z
                    $d    = date('d', strtotime($enddate));//get date day
                    $m    = date('m', strtotime($enddate));//get date month
                    $y    = date('Y', strtotime($enddate));//get date year
                    //$now = date('Y-m-d G:i:s');//current date and time
                    $enddate = date('Y-m-d', strtotime($enddate));//user friendly date
    			    //echo "event end date: ".$enddate;
    			    
    			}
    			else
    			{
    			    //print_r("   event non: ".$key);
    			}
    		}
    		$date = $startdate;
    		while (strtotime($date) <= strtotime($enddate)) {

                $isExistDate = DB::table('occupancy_info')
                                    ->where(['property_id' => $property->property_id])
                                    ->get();
                
                if ($isExistDate) {
                    foreach($isExistDate as $rowDate) {
                        $checkDataExist = DB::table('occupancy_info')
                            ->where(
                                [
                                    'property_id' => $property->property_id,
                                    'date' => $rowDate->date
                                ]
                            )->get();
                            if(!$checkDataExist) {
                                DB::table('occupancy_info')
                                    ->where('id', $property->property_id)
                                    ->update(['status' => 0]);
                            }
                    }
                }
                DB::table('occupancy_info')
                                ->updateOrInsert(
                                    ['property_id' => $property->property_id, 'date'=> $date ],
                                    ['property_rent_id'=> 1, 'rent' => '', 'occupancy_status' => 1, 'status'=> 1 ]
                                );
               
                //echo '<p>'.$roomid.'***'.'Date:'.$date.'propertyId'.$property->property_id.'</p>';
                $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
	        }
    	}
    } 
    }
}
