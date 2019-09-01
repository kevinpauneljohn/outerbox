<?php

namespace App\Http\Controllers;

use App\address\Region;
use App\Models\CallCenter;
use App\Ticket;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        return view('Employee.Agent.tickets')->with([
            'tickets' => $tickets,
            'lgus'    => $this->get_registered_lgu($this->get_user_call_center())
        ]);

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

    public function call_user()
    {
// Your Account SID and Auth Token from twilio.com/console
        $account_sid = 'ACc2b621c75962600764b5dc43529b4fcd';
        $auth_token = '10393bb1636198ac1ff9811e5e3b1152';
        // In production, these should be environment variables. E.g.:
        // $auth_token = $_ENV["TWILIO_ACCOUNT_SID"]

        // A Twilio number you own with Voice capabilities
        $twilio_number = "+19282183974";

        // Where to make a voice call (your cell phone?)
        $to_number = "+639051583899";

        $client = new Client($account_sid, $auth_token);
        $client->account->calls->create(
            $to_number,
            $twilio_number,
            array(
                "url" => "http://demo.twilio.com/docs/voice.xml"
            )
        );
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
}
