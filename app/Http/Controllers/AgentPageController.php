<?php

namespace App\Http\Controllers;

use App\address\Region;
use App\Models\CallCenter;
use App\Ticket;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Nexmo\Client\Credentials\Keypair;
use Nexmo\Client;
use Nexmo\Laravel\Facade\Nexmo;
use Illuminate\Support\Carbon;

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

    public function call_user()
    {
        $keypair = new Keypair(
            file_get_contents(base_path('/private.key')),
            '271d2050-e635-4432-abf9-9382ad560b54'
        );

        $client = new Client($keypair);

//        $call = $client->calls()->create([
//            'to' => [[
//                'type' => 'phone',
//                'number' => 639051583899
//            ]],
//            'from' => [
//                'type' => 'phone',
//                'number' => 6322313601
//            ],
//            'answer_url' => ['https://developer.nexmo.com/ncco/tts.json'],
//        ]);

        Nexmo::calls()->create([
            'to' => [[
                'type' => 'phone',
                'number' => '639051583899'
            ]],
            'from' => [
                'type' => 'phone',
                'number' => '6322313601'
            ],
            'answer_url' => ['https://developer.nexmo.com/ncco/tts.json'],
            'event_url' => ['http://b1d0ee42.ngrok.io/webhooks/events']
        ]);
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
