<?php

namespace App\Http\Controllers\Ticket;

use App\Events\RequestVerified;
use App\Lgu;
use App\Ticket;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TestEventController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Date;

class TicketController extends Controller
{

    /**
     * update the ticket status
     * @param Request $request
     * route /update_ticket_status
     * @return Response
     * */
    public function update_ticket_status(Request $request)
    {

        $ticket = Ticket::find($request->ticket_id);
        $ticket->status = $request->value;

        $message = ($ticket->save()) ? ['success'=>true] : ['success' => false];
        return response()->json($message);
    }


    /**
     * assign the lgu to a ticket thru lgu id
     * @param Request $request
     * route /assign-lgu-to-ticket
     * @return Response
     * */
    public function assign_lgu_to_ticket(Request $request)
    {

        $ticket = Ticket::find($request->ticket_id);
        $ticket->lgu_id = $request->lgu_id;
        $ticket->status = 'On-going';
        $ticket->save() ? $message = ['success' => true] : ['success' => false];
        return response()->json($message);
//        return $request->ticket_id;
    }


    /**
     * Display the label of app_response in the view
     * @param Request $request
     * @return array
     * */
    public function display_lead_details(Request $request)
    {
        $tickets = DB::table('leads')
            ->leftJoin('tickets','leads.id','=','tickets.lead_id')
            ->select('leads.app_response')
            ->where('tickets.id','=',$request->ticket_id)
            ->first();

        $app_response = $tickets->app_response;
        $requested_date = $this->display_label($app_response,'request_date',10);
        $requested_date  = str_replace('\/','-',$requested_date);
        $data = [
            'firstname' => $this->display_label($app_response,'firstname',1),
            'middlename' => $this->display_label($app_response,'middlename',2),
            'lastname' => $this->display_label($app_response,'lastname',3),
            'latitude' => $this->display_label($app_response,'lat',4),
            'longitude' => $this->display_label($app_response,'lang',5),
            'region' => $this->display_label($app_response,'region',6),
            'zip_code' => $this->display_label($app_response,'zip_code',7),
            'city' => $this->display_label($app_response,'city',8),
            'province' => $this->display_label($app_response,'province',9),
            'request_date' => $requested_date,
            'request_time' => $this->display_label($app_response,'request_time',11),
            'mobile_no' => $this->display_label($app_response,'mobile_no',12),
//            'timestamp' => $this->display_label($app_response,'timestamp',13),
            'type_of_accident' => $this->display_label($app_response,'type_of_accident',13),
            'emergency_contact' => $this->display_label($app_response,'emergency_contact',14),
            'contact_no' => $this->display_label($app_response,'contact_no',15),
        ];
        //return $this->display_label($app_response,'emergency_contact',14);
        return $data;
    }

    /**
     * Display the label of app_response retrieved in leads table
     * @param string $app_response
     * @param string $name
     * @param  int $index
     * @return array
     */
    public function display_label($app_response, $name,$index)
    {
        $str = $app_response;
        $split = explode(',',$str);

        $region = explode('"'.$name.'":',$split[$index]);
        $reg = explode('"',$region[1]);

        return $reg[1];
    }


    /**
     * Connect leads to lgu
     * @param Request $request
     * @return array
     */
    public function connect_to_lgu(Request $request)
    {
        /**
         * get the response team name, officer in charge, agent
         * @var $stationName
         * @var $officeInCharge
         * @var $agent
         * @var $callCenterId
         * */

        // $lgu = Lgu::find($request->lgu_id);
        // $stationName = $lgu->station_name;
        // $officeInCharge = $lgu->contactpeople[0]->fullname;
        // $agent = auth()->user()->username;
        // $callCenter = User::find(auth()->user()->id)->callcenter[0]->name;

        // $data = array(
        //     'responseTeam'      => $stationName,
        //     'officeInCharge'    => $officeInCharge,
        //     'agent'             => $agent,
        //     'callCenter'        => $callCenter);

        //return $data;

        // Update By Jovito Pangan, Oct. 11, 2019
        // This code will update the duration_until_agent_transfer_request

        $lead_ticket_id = DB::table('leads')
            ->leftJoin('tickets','leads.id','=','tickets.lead_id')
            ->select('leads.app_user_id')
            ->where('tickets.id','=',$request->ticket_id)
            ->first();
        $ticket = Ticket::find($request->ticket_id);
        $ticket->duration_until_agent_transfer_request = now();
        event(new RequestVerified($lead_ticket_id->app_user_id,'Confirmed'));
        $ticket->save() ? $message = ['success' => true] : ['success' => false];

        return response()->json($message);
    }


    /**
     * create child ticket
     * date created 05/29/2019
     * @param int $parentTicketID
     * @param int $leadId
     * @param int $callCenterId
     * @param int $agentId
     * @param datetime $dateReported
     * @return boolean
     * */
    public function create_child_ticket($parentTicketID, $leadId, $callCenterId, $agentId, $dateReported)
    {
        $ticket = new Ticket;
        $ticket->lead_id = $leadId;
        $ticket->call_center_id = $callCenterId;
        $ticket->user_assigned_id = $agentId;
        $ticket->user_created_id = 0;
        $ticket->date_reported = $dateReported;
        $ticket->status = 'Pending';

        if($ticket->save())
        {
            $childTicket = DB::table('parent_ticket')
                ->insert([
                    ['ticket_id' => $ticket->id],
                    ['parent_ticket_id' => $parentTicketID],
                    ['created_at' => Carbon::now()],
                    ['updated_at' => Carbon::now()],
                ]);
            return $childTicket ? true : false;
        }
//        return ($ticket->save()) ? true : false;
    }

    /**
     * relate ticket to parent ticket
     * @param Request $request
     * @return Response
     * */
    public function relate_tickets(Request $request)
    {
        $childTicket = DB::table('parent_ticket')->insert([
            'ticket_id' => $request->ticketId,
            'parent_ticket_id' => $request->ticketList,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $message = $childTicket ? ['success' => true] : ['success' => false];
        return response()->json($message);
    }

    /**
     * get the id of the parent ticket
     * date: 09/06/2019
     * @param int $ticketId
     * @return mixed
     * */
    public static function get_parent_ticket($ticketId)
    {
        $parentTicket = DB::table('tickets')
            ->leftJoin('parent_ticket','tickets.id','=','parent_ticket.ticket_id')
            ->select('parent_ticket.parent_ticket_id')
            ->where('tickets.id',$ticketId)
            ->first();

        return $parentTicket->parent_ticket_id != null ? CreateTicketController::getSequence($parentTicket->parent_ticket_id) : '';
    }

    public function twilio_callback(Request $request)
    {

    }

//    public function time_handled($date1, $date2)
//    {
//        $date1=strtotime($date1);
//        $date2=strtotime($date2);
//        $diff = abs($date1 - $date2);
//
//        $day = $diff/(60*60*24); // in day
//        $dayFix = floor($day);
//        $dayPen = $day - $dayFix;
//        if($dayPen > 0)
//        {
//            $hour = $dayPen*(24); // in hour (1 day = 24 hour)
//            $hourFix = floor($hour);
//            $hourPen = $hour - $hourFix;
//            if($hourPen > 0)
//            {
//                $min = $hourPen*(60); // in hour (1 hour = 60 min)
//                $minFix = floor($min);
//                $minPen = $min - $minFix;
//                if($minPen > 0)
//                {
//                    $sec = $minPen*(60); // in sec (1 min = 60 sec)
//                    $secFix = floor($sec);
//                }
//            }
//        }
//        $str = "";
//        if($dayFix > 0)
//            $str.= $dayFix." day ";
//        if($hourFix > 0)
//            $str.= $hourFix." hour ";
//        if($minFix > 0)
//            $str.= $minFix." min ";
//        if($secFix > 0)
//            $str.= $secFix." sec ";
//        return $str;
//    }

    /**
     * Update Ticket after triggering a call
     * Created Date: Oct 11, 2019
     * By: Jovito Pangan
     * @param Request request
     * @return message
     */
    public function update_field_after_call(Request $request){
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://api.crazycall.com/api/',
            'headers' => ['x-api-token' => 'f8DAYKDzK0JHRyOdZvTDeiTMN9vZtoCv', 'Account' => 'inbyz.crazycall.com'],
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);

        $response = $client->request('GET', 'v1/calls?limit=3&page=1');
        $get_id = $response->getBody();
        //error_log(print_r($response->getBody()));
        //print_r($response->getResult());

        $callRecord = json_decode($get_id);
        error_log($callRecord[0]->lengthSeconds);
        error_log(getType($callRecord[0]->answeredDate));
        error_log($callRecord[0]->finishDate);

        $time = strtotime($callRecord[0]->answeredDate);
        $newformat = date('Y-m-d H:i:s',$time);

        $time2 = strtotime($callRecord[0]->finishDate);
        $newformat2 = date('Y-m-d H:i:s',$time2);

        $ticket = Ticket::find($request->ticket_id);
        $ticket->call_duration = $callRecord[0]->lengthSeconds;
        $ticket->time_handled = $newformat;
        $ticket->duration_before_agent_handled_call = $newformat2;
        $ticket->status = 'On-going';
        $message = ($ticket->save()) ? ['success'=>true] : ['success' => false];

        $lead_ticket_id = DB::table('leads')
            ->leftJoin('tickets','leads.id','=','tickets.lead_id')
            ->select('leads.app_user_id')
            ->where('tickets.id','=',$request->ticket_id)
            ->first();
        event(new RequestVerified($lead_ticket_id->app_user_id,'Verified'));
        return response()->json($message);
    }

    // Create a channel that will message the request verified or request confirmed
    // create a channel for LGU and app that will tell the LGU app to verify request and
    // update mobile app user progress tracker


    /**
     * This method will calculate seconds between two dates (that can be use for hours, days, weeks, etc.)
     * @param dates
     * November 13, 2019
     * @author Jovito Pangan
     */
    public function calculate_seconds_of_dates($date1, $date2){

        $timeFirst  = strtotime($date1);
        $timeSecond = strtotime($date2);
        $diffSeconds = $timeSecond - $timeFirst;
        return $diffSeconds;
    }

}
