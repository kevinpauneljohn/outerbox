<?php

namespace App\Http\Controllers;

use App\activity;
use App\address\Region;
use App\Http\Controllers\address\AddressController;
use App\Models\CallCenter;
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

    /**
     * employee page
     * @return mixed
     * */
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
        /*$user = User::find($id);
        return view('Employee.agentProfile')->with(['user' => $user]);*/

        $user = User::find($id);
        $activities = activity::where('user_id',$id)->get();
        return view('Employee.agentProfile')->with([
            'user'          => $user,
            'activities'    => $activities,
            "dateTime"          => new TimeController,
            "roles"             => new RolesController,
            "roleList"          => Role::where('name','!=','Lgu')->get(),
            "callCenterUser"        => User::find($id)->callcenter()->first(),
            "active"            => User::where([['id','=',$id],['active','=',1]]),
            "userDetails"            => User::where([['id','=',$id]]),
        ]);
    }

    public function lgu()
    {
        $user = User::find(Auth::user()->id)->callcenter;
        $callcenter_id = $user[0]->pivot->cc_id;
        $callCenter = CallCenter::all();
        $regions = Region::all();

        $lgus = DB::table('lgus')
            ->leftJoin('call_centers','lgus.call_center_id','=','call_centers.id')
            ->leftJoin('contact_people','lgus.id','=','contact_people.lgu_id')
            ->leftJoin('users','contact_people.user_id','=','users.id')
            ->select('lgus.id as lgu_id','lgus.station_name','lgus.department','lgus.created_at','lgus.region','lgus.province','lgus.city','lgus.address',
                'call_centers.id as cc_id',
                'contact_people.contactno',
                'users.firstname','users.lastname','users.username')
            ->where([
                ['lgus.call_center_id','=',$callcenter_id],
                ['lgus.deleted_at','=',null]
            ]);

        return view('Employee.lgu')->with([
            'lgus'    => $lgus,
            'regions' => $regions,
            "callCenters"    => $callCenter,
            "address"       => new AddressController,
            "callCenterId"  => $callcenter_id,
        ]);

    }
}
