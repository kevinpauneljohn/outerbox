<?php

namespace App\Http\Controllers\Ticket;

use App\Ticket;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
            'timestamp' => $this->display_label($app_response,'timestamp',13),
            'type_of_accident' => $this->display_label($app_response,'type_of_accident',14),
            'emergency_contact' => $this->display_label($app_response,'emergency_contact',15),
            'contact_no' => $this->display_label($app_response,'contact_no',16),
        ];
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
     * @return void
     */
    public function connect_to_lgu(Request $request)
    {
//        return $request->all();
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
        $status = $request->all();

        $twilio_response = DB::table('twilio_callback')
            ->insert(['callback_response' => $status]);
    }

}
