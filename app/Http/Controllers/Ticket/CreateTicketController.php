<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\address\AddressController;
use App\Models\CallCenter;
use App\Models\Lead;
use App\Ticket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class CreateTicketController extends Controller
{
//    this method will check the new leads in leads table
    private function check_leads()
    {
        $new_leads = Lead::where('status','new');
        return $new_leads;
    }

    //this method assign the ticket to the nearest call center
    private function get_call_center_id($request_location)
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
            ])->first();

        return ($call_centers != null) ? $call_centers->id : 0;
    }

    private function get_available_agent($call_center_id)
    {
        $employees = DB::table('users')
            ->leftJoin('callcenterdetails','users.id','=','callcenterdetails.user_id')
            ->leftJoin('model_has_roles','users.id','=','model_has_roles.model_id')
            ->leftJoin('roles','model_has_roles.role_id','=','roles.id')
            ->select('users.id')
            ->where([
                ['callcenterdetails.cc_id','=',$call_center_id],
                ['users.deleted_at','=',null],
                ['roles.name','=','agent']
            ])
            ->first();

        return $employees->id;
    }
  
    // this method is used to create tickets
    private function create_ticket($lead_id, $call_center_id, $agent_id, $date_reported)
    {
        $ticket = new Ticket;
        $ticket->lead_id = $lead_id;
        $ticket->call_center_id = $call_center_id;
        $ticket->user_assigned_id = $agent_id;
        $ticket->user_created_id = 0;
        $ticket->date_reported = $date_reported;
        $ticket->status = 'pending';

        return ($ticket->save()) ? true : false;
    }

    //this method will be called by CRON to retrieve all new leads and automatically create ticket
    public function get_all_new_leads()
    {
        if($this->check_leads()->count() > 0)
        {
            foreach($this->check_leads()->get() as $lead)
            {
                $call_center_id = $this->get_call_center_id('blk 141 lot 2, ANGELES CITY, PAMPANGA, REGION III (CENTRAL LUZON)');
                //$agent_id = $this->get_available_agent($call_center_id);

                $this->create_ticket(
                    $lead->id,
                    $call_center_id,
                    9,
                    $lead->created_at
                    );

                $this->update_lead_status($lead->id, null);
            }
        }
    }

    private function update_lead_status($lead_id, $status)
    {
        $lead = Lead::find($lead_id);
        $lead->status = $status;
        $lead->save();
    }

    public static function getSequence($num) {
        return sprintf("%'.09d\n", $num);
    }

    public static function get_status_label($status)
    {
            switch ($status) {
                case 'pending':
                    $label = 'orange';
                    break;
                case 'missed':
                    $label = 'red';
                    break;
                case 'prank':
                    $label = 'yellow';
                    break;
                case 'completed':
                    $label = 'green';
                    break;
            }

        return $label;
    }
}
