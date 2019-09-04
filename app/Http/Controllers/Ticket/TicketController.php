<?php

namespace App\Http\Controllers\Ticket;

use App\Ticket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function update_ticket_status(Request $request)
    {
        $ticket = Ticket::find($request->ticket_id);
        $ticket->status = $request->value;

        $message = ($ticket->save()) ? ['success'=>true] : ['success' => false];
        return response()->json($message);
    }

    public function assign_lgu_to_ticket(Request $request)
    {
        $ticket = Ticket::find($request->ticket_id);
        $ticket->lgu_id = $request->lgu_id;
        $ticket->status = 'On-going';
        $ticket->save() ? $message = ['success' => true] : ['success' => false];
        return response()->json($message);
//        return $request->ticket_id;
    }

    public function display_lead_details(Request $request)
    {
        $tickets = DB::table('leads')
            ->leftJoin('tickets','leads.id','=','tickets.lead_id')
            ->select('leads.*')
            ->where('tickets.id','=',$request->ticket_id)
            ->first();

        $app_response = $tickets->app_response;
        $data = [
            'firstname' => $this->display_json_value($app_response,'firstname',1),
            'lastname' => $this->display_json_value($app_response,'lastname',3),
            'latitude' => $this->display_json_value($app_response,'lat',4),
            'longitude' => $this->display_json_value($app_response,'lang',5),
            'region' => $this->display_json_value($app_response,'region',6),
            'zip_code' => $this->display_json_value($app_response,'zip_code',7),
            'city' => $this->display_json_value($app_response,'city',8),
            'province' => $this->display_json_value($app_response,'province',9),
            'request_date' => $this->display_json_value($app_response,'request_date',10),
            'request_time' => $this->display_json_value($app_response,'request_time',11),
            'mobile_no' => $this->display_json_value($app_response,'mobile_no',12),
            'timestamp' => $this->display_json_value($app_response,'timestamp',13),
            'type_of_accident' => $this->display_json_value($app_response,'type_of_accident',14),
            'emergency_contact' => $this->display_json_value($app_response,'emergency_contact',15),
            'contact_no' => $this->display_json_value($app_response,'contact_no',16),
        ];
        return $data;
    }

    public function display_json_value($app_response, $name,$index)
    {
        $str = $app_response;
        $split = explode(',',$str);

        $region = explode('"'.$name.'":',$split[$index]);
        $reg = explode('"',$region[1]);

        return $reg[1];
    }

    /*
     * connect to lgu
     * */
    public function connect_to_lgu(Request $request)
    {
        return $request->all();
    }
}
