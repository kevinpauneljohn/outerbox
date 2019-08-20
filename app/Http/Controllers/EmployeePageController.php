<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;


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
        $role = Role::all()->except(['1']);

        $employees = DB::table('users')
            ->leftJoin('callcenterdetails','users.id','=','callcenterdetails.user_id')
            ->leftJoin('model_has_roles','users.id','=','model_has_roles.model_id')
            ->leftJoin('roles','model_has_roles.role_id','=','roles.id')
            ->select('users.id','users.firstname','users.middlename','users.lastname','users.email','users.username','users.created_at','roles.name as role_name','callcenterdetails.cc_id as cc_id')
            ->where([
                ['callcenterdetails.cc_id','=',$assignedCallCenter[0]->cc_id],
                ['users.id','!=',$user_id],
                ['users.deleted_at','=',null]
            ])
            ->get();

        return view('Employee.agent')->with([
            'user_id' => $user_id,
            'cc_id' => $assignedCallCenter[0]->cc_id,
            'employees' => $employees,
            'roles' => $role
        ]);
    }

    public function agentProfile($id)
    {
        $user = User::find($id);
        return view('Employee.agentProfile')->with(['user' => $user]);
    }

    public function lgu()
    {
        return view('Employee.lgu');
    }
}
