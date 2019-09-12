<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
//use App\Http\Controllers\Controller;
use Twilio\Jwt\ClientToken;

class TokenController extends Controller
{
    /**
     * Create a new capability token
     *
     * @return \Illuminate\Http\Response
     */
    public function newToken(Request $request)
    {
 //       $clientToken = new ClientToken();
        $forPage = $request->input('forPage');
 //       $applicationSid = config('services.twilio')['applicationSid'];
//        $clientToken->allowClientOutgoing($applicationSid);
//
//        if ($forPage === '/agent/ticket') {
//            $clientToken->allowClientIncoming('support_agent');
//        } else {
//            $clientToken->allowClientIncoming('customer');
//        }
//
//        $token = $clientToken->generateToken();
        return response()->json(['token' => new ClientToken()]);
    }
}
