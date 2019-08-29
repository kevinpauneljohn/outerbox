<?php

namespace App\Http\Controllers\Leads;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeadsController extends Controller
{
    public function get_response(Request $request)
    {
        return $request->all();
    }
}
