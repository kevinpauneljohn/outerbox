<?php

namespace App\Http\Controllers\SuperAdmin;

use App\activity;
use App\address\Region;
use App\Lgu;
use App\Models\Lead;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use App\Models\CallCenter;
use App\Http\Controllers\Reports\Reports;

class SuperAdminController extends Controller
{
    /**
     * date: oct 05, 2019
     * by: john kevin paunel
     * activity logs variable
     * @var $activity
     * */
    private $activity;

    /**
     * date: oct. 05, 2019
     * by: john kevin paunel
     * this will initialized the report controller
     * @return void
     * */
    public function __construct()
    {
        $this->activity = new Reports;
    }

    public function dashboard()
    {
        return view('SuperAdmin.dashboard');
    }

    public function employee()
    {
        $roles = Role::all();
        $role = $roles->except(1);
        //$users = User::all()->except(2);

        $users = DB::table('users')
            ->leftJoin('callcenterdetails', 'users.id', '=', 'callcenterdetails.user_id')
            ->leftJoin('call_centers','callcenterdetails.cc_id','=','call_centers.id')
            ->leftJoin('model_has_roles','users.id','=','model_has_roles.model_id')
            ->leftJoin('roles','model_has_roles.role_id','=','roles.id')
            ->select('users.id','users.firstname','users.middlename','users.lastname','users.email','users.username','users.created_at','roles.name as role_name','callcenterdetails.cc_id as cc_id','call_centers.name as cc_name')
            ->where([
                ['users.id','!=',1],
                ['users.deleted_at','=',null]
            ])
            ->get();

        $callcenter = CallCenter::all();
        $time = Carbon::now();

        return view('SuperAdmin.employee.employee')->with(['roles' => $role, 'users' => $users, 'callcenters' => $callcenter, 'date' => $time]);
    }

    /**
     * Employee Profile Page View
     * @param int $id
     * @return mixed
     * */
    public function employeeProfile($id)
    {
        $user = User::find($id);
        $activities = activity::where('user_id',$id)->get();
        return view('SuperAdmin.employee.employeeProfile')->with([
            'user'          => $user,
            'activities'    => $activities
        ]);
    }

    #Role Page Method
    public function roles()
    {
        $roles = Role::all();
        return view('SuperAdmin.roles.roles')->with(['roles' => $roles]);
    }

    #validate and add new role submitted at Role Page Super Admin Access
    public function roleFormValidation(Request $request)
    {
        $validator = Validator::make($request->All(),[
            'name'          => 'required|min:1|max:20',
            'description'   => 'required|max:100',
        ]);

        if($validator->passes())
        {
            $role = new Role;
            $role->name = $request->name;
            $role->guard_name = 'web';
            $role->description = $request->description;


            if($role->save())
            {
                /*activity log*/
                $action = "Added a new role: ".$request->name;
                $this->activity->activity_log($action);

                return response()->json(['success' => true]);
            }
        }
        return response()->json($validator->errors());
    }

    #delete role
    public function deleteRole(Request $request)
    {
        $role = Role::find($request->role);
        $action = "deleted a role: ".$role->name." with role ID: ".$request->role;
        if($role->delete())
        {
            /*activity log*/
            $this->activity->activity_log($action);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

    #get the name of the role by ID use for deletion in role page super admin
    public function getRoleName(Request $request)
    {
        $role = Role::find($request->id)->name;

        return response()->json(['role_name' => ucfirst($role)]);
    }

    /**
     * update the role details
     * @param Request $request
     * @return mixed
     * */
    public function updateRoleDetails(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'edit_name'         => 'required|min:3|max:30',
            'edit_description'  => 'required|max:100',
        ]);

        if($validator->passes())
        {
            $role = Role::find($request->role_value);
            $role->name = $request->edit_name;
            $role->description = $request->edit_description;

            /**
             * check if the submitted inputs have changes
             * @var $checkRoleInput
             * */
            $checkRoleInput = DB::table('roles')
                ->where([
                    ['name','=',$request->edit_name],
                    ['description','=',$request->edit_description]
                ]);
            if($checkRoleInput->count() < 1)
            {
                $oldRole = Role::find($request->role_value);
                $prevAction = "update the role from Role Name: ".$oldRole->name.", description: ".$oldRole->description;
                if($role->save())
                {
                    $action = "to Role Name: ".$request->edit_name.", description: ".$request->edit_description;
                    $this->activity->activity_log($prevAction." ".$action);
                    return response()->json(['success' => true]);
                }
            }else{
                return response()->json(['success' => 'No changes occurred']);
            }

        }

        return response()->json($validator->errors());
    }

    #get role details
    public function getRoleDetails(Request $request)
    {
        $role = Role::find($request->id);
        return $role;
    }

    #permissions page
    public function permissions()
    {
        $permission = Permission::all();
        return view('SuperAdmin.roles.permissions')->with(['permissions' => $permission]);
    }

    /**
     * add new permission
     * @param Request $request
     * @return Response
     * */
    public function permissionFormValidation(Request $request)
    {
        $validator = Validator::make($request->All(),[
            'permission_name'          => 'required|min:3|max:20|unique:permissions,name'
        ]);

        if($validator->passes())
        {

            if(Permission::create(['name' => $request->permission_name]))
            {
                /*activity log*/
                $action = "added new permission: ".$request->permission_name;
                $this->activity->activity_log($action);
                return response()->json(['success' => true]);
            }
        }
        return response()->json($validator->errors());
    }

    public function callCenter()
    {
        $callCenter = CallCenter::all();
        $region = Region::all();

        return view('SuperAdmin.callCenter.callcenter')->with(['callcenters' => $callCenter, 'regions' => $region]);
        //return $callcenter;
    }



    public function callCenterProfile($id)
    {

        $callCenter = CallCenter::find($id);

        $employee = DB::table('users')
            ->leftJoin('callcenterdetails', 'users.id', '=', 'callcenterdetails.user_id')
            ->leftJoin('model_has_roles','users.id','=','model_has_roles.model_id')
            ->leftJoin('roles','model_has_roles.role_id','=','roles.id')
            ->select('users.*','roles.name as role_name')
            ->where([
                ['users.id','!=',2],
                ['users.deleted_at','=',null],
                ['callcenterdetails.cc_id', '=',$id]
            ])
            ->get();

        $roles = Role::all()->except(1);

        return view('SuperAdmin.callCenter.callcenterProfile')->with([
            'callcenter' => $callCenter,
            'employees' => $employee,
            'roles' => $roles,
            'cc_id' => $id
        ]);
    }


    /**
     * super admin LGU page view
     *
     * @return mixed
     * */
    public function lgu()
    {
        $callCenter = CallCenter::all();
        $regions = Region::all();
        $lgus = DB::table('lgus')
            ->leftJoin('call_centers','lgus.call_center_id','=','call_centers.id')
            ->leftJoin('contact_people','lgus.id','=','contact_people.lgu_id')
            ->select('lgus.id as lgu_id','lgus.station_name','lgus.department','lgus.created_at','lgus.region','lgus.province','lgus.city','lgus.address',
                'call_centers.id as cc_id',
                'contact_people.fullname as contactname','contact_people.contactno')
            ->where('lgus.deleted_at','=',null
            );

        return view('SuperAdmin.lgu.lgu')->with([
            "lgus"          => $lgus,
            "regions"       => $regions,
            "callCenters"    => $callCenter
        ]);
    }

    /**
     * date: oct. 10, 2019
     * by: John Kevin Paunel
     * Activity view page
     * @return mixed
     * */
    public function activities()
    {
        $activities = activity::all();
        return view("SuperAdmin.Activity.activity")->with([
            "activities"        => $activities,
        ]);
    }


    /**
     * for mobile app
     * display of call center list
     * */
    public function show_call_center_list()
    {
        return CallCenter::all()->pluck('postalcode');
    }

    public function show_lgu_list()
    {
        return Lgu::all();
    }
}
