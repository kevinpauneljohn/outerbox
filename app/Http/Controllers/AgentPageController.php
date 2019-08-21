<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgentPageController extends Controller
{
    public function dashboard()
    {
        return view('Employee.Agent.dashboard');
    }
}
