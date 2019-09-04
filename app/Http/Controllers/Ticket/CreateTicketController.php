<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\address\AddressController;
use App\Models\CallCenter;
use App\Models\Lead;
use App\Ticket;
use App\User;
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
    private function get_call_center_id($region,$state, $city)
    {
        /*
         * the format should be $request_location = "address, city, state, region";
         */

        //$address = $this->get_all_local_call_center('blk 141 lot 2, ANGELES CITY, PAMPANGA, REGION III (CENTRAL LUZON)');
//        $request_location = explode(', ',$request_location);
        $call_centers = DB::table('call_centers')
            ->leftJoin('refcitymun','call_centers.city','=','refcitymun.citymunCode')
            ->leftJoin('refregion','call_centers.region','=','refregion.regCode')
            ->leftJoin('refprovince','refregion.regCode','=','refprovince.regCode')
            ->select('call_centers.name', 'call_centers.id')
            ->where([
                ['refregion.regDesc','=',$region],
                ['refprovince.provDesc','=',$state],
                ['refcitymun.citymunDesc','=',$city]
            ])->first();

         return ($call_centers != null) ? $call_centers->id : 0;
    }

    /*
     * return total number of active agents
    */
    private function count_active_agents($call_center_id)
    {
        return CallCenter::find($call_center_id)->users->where('active',1)->count();
    }

    private function get_available_agent($call_center_id)
    {
        $total_agents = $this->count_active_agents($call_center_id);
        $employees = DB::table('users')
            ->leftJoin('callcenterdetails','users.id','=','callcenterdetails.user_id')
            ->leftJoin('model_has_roles','users.id','=','model_has_roles.model_id')
            ->leftJoin('roles','model_has_roles.role_id','=','roles.id')
            ->select('users.id')
            ->where([
                ['callcenterdetails.cc_id','=',$call_center_id],
                ['users.deleted_at','=',null],
                ['users.active','=',1],
                ['roles.name','=','agent']
            ])
            ->get();

        return $employees != null ? $employees : null;
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
        $ticket->status = 'Pending';

        return ($ticket->save()) ? true : false;
    }

    //this method will be called by CRON to retrieve all new leads and automatically create ticket
    public function get_all_new_leads()
    {
        if($this->check_leads()->count() > 0)
        {
            $arr = [];
            $arr2 = [];
            $arr3 = [];
            $arr4 = [];
            foreach($this->check_leads()->get() as $lead)
            {

                $region = $lead->app_response[0]['region'];
                $state = $lead->app_response[0]['province'];
                $city = $lead->app_response[0]['city'];

                $call_center_id = $this->get_call_center_id($region, $state, $city);

                array_push($arr, $call_center_id);
                array_push($arr2, $lead->id);
            }
            ///will create ticket inside the loop
            foreach ($arr as $callcenter){

                $agents = $this->get_available_agent($callcenter);
                foreach ($agents as $agent){
                    if($agent != null){
                        array_push($arr3, $agent->id);
                        //echo $agent->id.'<br/>';
                    }


                }

                if($agents != null){
                    $user_agent = array_unique($arr3);
                }
            }
            $i = 0;
            $y = 0;
            foreach ($arr2 as $callcenter2){
                $select_agent = array_unique($arr3);
                $index_in_agent_count = $this->count_assigned_ticket(array_unique($arr3));
                //echo $select_agent[$index_in_agent_count];

                $lead_id = $arr2[$i++];
                $cc_id = $arr[$y++];
                $this->create_ticket(
                    $lead_id,
                    $cc_id,
                    $select_agent[$index_in_agent_count],
                    $lead->created_at
                    );

                $this->update_lead_status($lead_id, null);
            }

        }
    }

    public function count_assigned_ticket($user_id)
    {

        $count_list = [];
        foreach($user_id as $id)
        {
            array_push($count_list, User::find($id)->tickets->count());
        }
        return array_search(min($count_list), $count_list);
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

    public function tester()
    {
        $employees = DB::table('users')
            ->leftJoin('callcenterdetails','users.id','=','callcenterdetails.user_id')
            ->leftJoin('model_has_roles','users.id','=','model_has_roles.model_id')
            ->leftJoin('roles','model_has_roles.role_id','=','roles.id')
            ->select('users.id')
            ->where([
                ['callcenterdetails.cc_id','=',1],
                ['users.active','=',1],
                ['users.deleted_at','=',null],
                ['roles.name','=','agent']
            ])
            ->get();

        $agents = array();
        $count = 0;
        $leads = 5;
        foreach ($employees as $employee){
           echo $employee->id.' = '.$this->ticket_counter($employee->id).'<br/>';
           $agents[$count] = $employee->id;
            $count++;
        }

        $new_leads = Lead::all()->pluck('id');

    }


    /*
     * Count all assigned tickets from an agent
     * */
    public function ticket_counter($user_id)
    {
        return User::find($user_id)->tickets->count();
    }


}
