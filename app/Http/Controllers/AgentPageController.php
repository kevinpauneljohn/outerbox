<?php

namespace App\Http\Controllers;

use App\address\Region;
use App\Models\CallCenter;
use App\Ticket;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Twilio\Rest\Client;

class AgentPageController extends Controller
{
    public function dashboard()
    {
        return view('Employee.Agent.dashboard');
    }

    /*
    * Agent Leads Page
    */
    public function ticket()
    {
        $tickets = DB::table('tickets')
            ->leftJoin('lgus','tickets.lgu_id','=','lgus.id')
            ->leftJoin('call_centers','tickets.call_center_id','=','call_centers.id')
            ->leftJoin('users','tickets.user_assigned_id','=','users.id')
            ->leftJoin('leads','tickets.lead_id','=','leads.id')
            ->select(
                'call_centers.name as call_center_name','call_centers.id as callcenter_id',
                'users.username',
                'leads.id as lead_id','leads.app_user_id','leads.created_at as date_reported','leads.app_response',
                'lgus.*',
                'tickets.*')
            ->where('tickets.user_assigned_id','=',auth()->user()->id)
            ->get();
        $callCenterId = User::find(auth()->user()->id)->callcenter()->pluck('cc_id')[0];
        $callCenterTickets = CallCenter::find($callCenterId)->tickets()->pluck('id');

//        return $callCenterTickets;

        return view('Employee.Agent.tickets')->with([
            'tickets' => $tickets,
            'lgus'    => $this->get_registered_lgu($this->get_user_call_center()),
            'callCenterTickets' => $callCenterTickets
        ]);

//        foreach ($tickets as $ticket){
//            $str = $ticket->app_response;
//            $split = explode(',',$str);
//
//            $region = explode('"mobile_no":',$split[12]);
//            $reg = explode('"',$region[1]);
//
//            echo $reg[1]."<br/>";
//
//        }



//        echo $tickets;
    }
    public static function get_mobile_no($app_response)
    {
        $str = $app_response;
        $split = explode(',',$str);

        $region = explode('"mobile_no":',$split[12]);
        $reg = explode('"',$region[1]);

        return $reg[1];
    }

    public static function get_requested_date($app_response)
    {
        $str = $app_response;
        $split = explode(',',$str);

        $region = explode('"request_date":',$split[10]);
        $reg = explode('"',$region[1]);

        $requested_date = str_replace('\/','-',$reg[1]);
        return $requested_date.' '.self::get_requested_time($app_response);
    }

    public static function get_requested_time($app_response)
    {
        $str = $app_response;
        $split = explode(',',$str);

        $region = explode('"request_time":',$split[11]);
        $reg = explode('"',$region[1]);

        return $reg[1];
    }

    public static function get_app_response($app_response)
    {
        $str = $app_response;
        $split = explode(',',$str);

        $region = explode('"region":',$split[6]);
        $reg = explode('"',$region[1]);
        ///;
        echo self::get_city($app_response).', '.self::get_state($app_response).', '.$reg[1];
    }

    private static function get_city($app_response)
    {
        $str = $app_response;
        $split = explode(',',$str);

        $region = explode('"city":',$split[8]);
        $reg = explode('"',$region[1]);

        return $reg[1];
    }

    private static function get_state($app_response)
    {
        $str = $app_response;
        $split = explode(',',$str);

        $region = explode('"province":',$split[9]);
        $reg = explode('"',$region[1]);

        return $reg[1];
    }
    /*
     * display all registered lgu per callcenter in ticket page
     * */
    private function get_registered_lgu($call_center_id)
    {
        return CallCenter::find($call_center_id)->lgus;
    }

    /*
     * get current logged user call center id
     * */
    private function get_user_call_center()
    {
        $user_id = auth()->user()->id;
        $call_center = User::find($user_id)->callcenter;
        return $call_center[0]->id;
    }

    public static function status()
    {
        return array('Pending','On-going','Prank','Completed');
    }

    public function test(Request $request)
    {
        return $request->all();
    }


    /**
     * call user
     * @param Request $request
     * @return mixed
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function call_user(Request $request)
    {
        $AccountSid = 'ACa2901d7449d60690cb960e94f5f56df2';
        $AuthToken = '24789a94f5f1775d0028bd477f928ca7';

        $client = new Client($AccountSid, $AuthToken);

        try{
            //$call = $client->calls->create($request->mobile_no,"+6326263521",
            $call = $client->calls->create("+639166520817","+6326263521",
                array(
                    "method" => "GET",
                    "statusCallback" => "http://crm.devouterbox.com/v1/events",
                    "statusCallbackEvent" => array("initiated","answered"),
                    "statusCallbackMethod" => "POST",
                    "url" => "http://demo.twilio.com/docs/voice.xml")
            );
            $startedCall = array('action' => 'ringing', 'callId' => $call->sid);

            //return $startedCall;
            print($call->sid);
        }catch (Exception $e){
            echo "Error: ".$e->getMessage();
        }
    }

    public function lgu()
    {
        $user = User::find(Auth::user()->id)->callcenter;
        $callcenter_id = $user[0]->pivot->cc_id;


        $lgus = DB::table('lgus')
            ->leftJoin('call_centers','lgus.call_center_id','=','call_centers.id')
            ->leftJoin('contact_people','lgus.id','=','contact_people.lgu_id')
            ->select('lgus.id as lgu_id','lgus.station_name','lgus.department','lgus.created_at','lgus.region','lgus.province','lgus.city','lgus.address',
                'call_centers.id as cc_id',
                'contact_people.fullname as contactname','contact_people.contactno')
            ->where('lgus.call_center_id','=',$callcenter_id);


        return view('Employee.Agent.lgu')->with(['lgus'=> $lgus]);
    }


    /**
     * display the details about the ticket
     * date: 09/06/2019
     * @param int $id
     * @return mixed
     * */
    public function ticket_profile_page($id)
    {
        return view('Employee.Agent.ticketProfile')->with(["ticketId" => $id]);
    }
}
