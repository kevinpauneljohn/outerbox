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
use LaravelDaily\LaravelCharts\Classes\LaravelChart;
use Twilio\Rest\Client;

class AgentPageController extends Controller
{
    public function dashboard()
    {
        $chart_options = [
            'chart_title' => 'Tickets by months',
            'report_type' => 'group_by_date',
            'model' => 'App\User',
            'relationship_name' => 'tickets',
            'group_by_field' => 'created_at',
            'group_by_period' => 'month',
            'chart_type' => 'bar',
        ];
        $chart1 = new LaravelChart($chart_options);

        /*$chart_option_tickets = [
            'chart_title' => 'Call Centers by months',
            'report_type' => 'group_by_date',
            'model' => 'App\Ticket',
            'group_by_field' => 'created_at',
            'group_by_period' => 'day',
            'chart_type' => 'line',
        ];
        $chart2 = new LaravelChart($chart_option_tickets);*/
        return view('Employee.Agent.dashboard', compact('chart1'));
    }

    /**
    * Agent Leads Page
     * @return mixed
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
            ->orderBy('created_at','asc');

        $callCenterId = User::find(auth()->user()->id)->callcenter()->pluck('cc_id')[0];
        $callCenterTickets = CallCenter::find($callCenterId)->tickets();

//        return $tickets->count();

        $dateTime = new TimeController();
        return view('Employee.Agent.tickets')->with([
            'tickets' => $tickets,
            'lgus'    => $this->get_registered_lgu($this->get_user_call_center()),
            'callCenterTickets' => $callCenterTickets,
            "dateTime"          => $dateTime,
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

//        return response()->json($tickets->get());

//        echo $tickets;
//        echo '<br>Difference is : '.$this->time_duration("2019-09-10 01:00:00", date('Y-m-d H:i:s'));
    }



    /**
     * this method will display the duration based on two dates
     * @param  string $date1
     * @param  string $date2
     * @return string
     * */
    public function time_duration($date1, $date2)
    {
        $date1=strtotime($date1);
        $date2=strtotime($date2);
        $diff = abs($date1 - $date2);

        $day = $diff/(60*60*24); // in day
        $dayFix = floor($day);
        $dayPen = $day - $dayFix;
        if($dayPen > 0)
        {
            $hour = $dayPen*(24); // in hour (1 day = 24 hour)
            $hourFix = floor($hour);
            $hourPen = $hour - $hourFix;
            if($hourPen > 0)
            {
                $min = $hourPen*(60); // in hour (1 hour = 60 min)
                $minFix = floor($min);
                $minPen = $min - $minFix;
                if($minPen > 0)
                {
                    $sec = $minPen*(60); // in sec (1 min = 60 sec)
                    $secFix = floor($sec);
                }
            }
        }
        $str = "";
        if($dayFix > 0)
            $str.= $dayFix." day ";
        if($hourFix > 0)
            $str.= $hourFix." hour ";
        if($minFix > 0)
            $str.= $minFix." min ";
        if($secFix > 0)
            $str.= $secFix." sec ";
        return $str;
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
        $newDate = strtotime($reg[1]);
        // $requested_date = str_replace('\/','-',$reg[1]);
        // return $requested_date.' '.self::get_requested_time($app_response);
        return date('d/M/Y H:i:s', $newDate);
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

//        try{
//            $call = $client->calls->create($request->mobile_no,"+6326263521",
////            $call = $client->calls->create("+639166520817","+6326263521",
//                array(
//                    "method" => "GET",
//                    "statusCallback" => "http://crm.devouterbox.com/v1/events",
//                    "statusCallbackEvent" => array("initiated","ringing","answered","completed"),
//                    "statusCallbackMethod" => "POST",
//                    "url" => "http://demo.twilio.com/docs/voice.xml")
//            );
//            $startedCall = array('action' => 'ringing', 'sid' => $call->sid);
//
//            //return $startedCall;
//            return $startedCall;
//        }catch (Exception $e){
//            echo "Error: ".$e->getMessage();
//        }
    }

    /**
     * Lgu Profile page view
     * route /agent/lgu
     * controller method AgentPageController@lgu
     * @return mixed
     * */
    public function lgu()
    {
        $user = User::find(Auth::user()->id)->callcenter;
        $callcenter_id = $user[0]->pivot->cc_id;


        $lgus = DB::table('lgus')
            ->leftJoin('call_centers','lgus.call_center_id','=','call_centers.id')
            ->leftJoin('contact_people','lgus.id','=','contact_people.lgu_id')
            ->leftJoin('users','contact_people.user_id','=','users.id')
            ->select('lgus.id as lgu_id','lgus.station_name','lgus.department','lgus.created_at','lgus.region','lgus.province','lgus.city','lgus.address',
                'call_centers.id as cc_id',
                'users.firstname','users.lastname','contact_people.contactno')
            ->where('lgus.call_center_id','=',$callcenter_id);


        return view('Employee.Agent.lgu')->with(['lgus'=> $lgus]);
    }

    /**
     * display the details about the ticket
     * @author john kevin paunel
     * date: 09/06/2019
     * route /ticket/{id}
     * @param int $id
     * @return mixed
     * */
    public function ticket_profile_page($id)
    {
        return view('Employee.Agent.ticketProfile')->with([
            "ticketId" => $id,
            "agent"    => $this->display_assigned_agent($id)
        ]);
    }

    /**
     * this method will return the agent value based on ticket ID
     * @param int $ticket_id
     * @return array
     * */
    public function display_assigned_agent($ticket_id)
    {
        return Ticket::find($ticket_id) != null ? Ticket::find($ticket_id)->users : "";
    }
}
