<?php

namespace App\Http\Controllers\Ticket;

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
    private function local_call_center()
    {

    }

    // this method is used to create tickets
    private function create_ticket($lead_id)
    {
        $ticket = new Ticket;
        $ticket->lead_id = $lead_id;
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
                $this->create_ticket($lead->id);
            }
        }
    }
}
