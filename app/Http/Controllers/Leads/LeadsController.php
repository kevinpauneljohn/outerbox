<?php

namespace App\Http\Controllers\Leads;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeadsController extends Controller
{
    public function get_response(Request $request)
    {
        $app_user_id = $request->input('app_user_id');
        $app_response = $request->input('app_response');

        $leads = new Lead;
        $leads->app_user_id = $app_user_id;
        $leads->app_response = $app_response;
        $leads->save();

        return response()->json(['success' => true]);
    }
}
