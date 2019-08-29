<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\address\AddressController;
use App\Models\Lead;
use App\Ticket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CreateTicketController extends Controller
{
//    this method will check the new leads in leads table
    private function check_leads()
    {
        $new_leads = DB::table('leads')->where('status','=','new')->count();
        return $new_leads;
    }

    //this method assign the ticket to the nearest call center
    private function get_all_local_call_center($request_location)
    {
        /*
         * the format should be $request_location = "address, city, state, region";
         */

        //$address = $this->get_all_local_call_center('blk 141 lot 2, ANGELES CITY, PAMPANGA, REGION III (CENTRAL LUZON)');
        $request_location = explode(', ',$request_location);
        $call_centers = DB::table('call_centers')
            ->leftJoin('refcitymun','call_centers.city','=','refcitymun.citymunCode')
            ->leftJoin('refregion','call_centers.region','=','refregion.regCode')
            ->leftJoin('refprovince','refregion.regCode','=','refprovince.regCode')
            ->select('call_centers.name', 'call_centers.id')
            ->where([
                ['refregion.regDesc','=',$request_location[3]],
                ['refprovince.provDesc','=',$request_location[2]],
                ['refcitymun.citymunDesc','=',$request_location[1]]
            ])->get();

        return $call_centers->id;
    }

    private function get_available_agent()
    {

    }
  
    // this method is used to create tickets
    private function create_ticket($lead_id, $call_center_id)
    {
        $ticket = new Ticket;
        $ticket->lead_id = $lead_id;
//        $ticket->call_center_id = $call_center_id;
        $ticket->call_center_id = $this->get_all_local_call_center('request_location');
        $ticket->user_assigned_id = 36;
        $ticket->user_created_id = 0;
        $ticket->status = 'pending';

        return ($ticket->save()) ? true : false;
    }

    //this method will be called by CRON to retrieve all new leads and automatically create ticket
    public function get_all_new_leads()
    {
        if($this->check_leads() > 0)
        {
            $leads = Lead::all();
            foreach($leads as $lead)
            {
                //$this->create_ticket($lead->id);
            }
        }
    }
}
