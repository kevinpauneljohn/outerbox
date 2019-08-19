<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class EmployeePageController extends Controller
{
    public function dashboard()
    {
        return view('Employee.employeeDashboard');
    }

    public function agent()
    {
        $user_id = Auth::user()->id;
        $assignedCallCenter = DB::table('callcenterdetails')->where('user_id','=',$user_id)->select('cc_id')->get();

        $employees = DB::table('users')
            ->leftJoin('callcenterdetails','users.id','=','callcenterdetails.user_id')
            ->select('users.*')
            ->where([
                ['callcenterdetails.cc_id','=',$assignedCallCenter[0]->cc_id],
                ['users.id','!=',$user_id]
            ])
            ->get();

        return view('Employee.agent')->with([
            'user_id' => $user_id,
            'assignedCallCenter' => $assignedCallCenter,
            'employees' => $employees
        ]);
    }

    public function lgu()
    {
        return view('Employee.lgu');
    }
}
