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
}
