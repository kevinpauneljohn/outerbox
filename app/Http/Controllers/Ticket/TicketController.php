<?php

namespace App\Http\Controllers\Ticket;

use App\Ticket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
}
