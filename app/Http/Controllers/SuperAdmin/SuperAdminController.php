<?php

namespace App\Http\Controllers\SuperAdmin;

use App\address\Region;
use App\Lgu;
use App\Models\Lead;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use App\Models\CallCenter;

class SuperAdminController extends Controller
{
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
                ['users.id','!=',2],
                ['users.deleted_at','=',null]
            ])
            ->get();

        $callcenter = CallCenter::all();
        $time = Carbon::now();

        return view('SuperAdmin.employee.employee')->with(['roles' => $role, 'users' => $users, 'callcenters' => $callcenter, 'date' => $time]);
    }

    public function employeeProfile($id)
    {
        $user = User::find($id);
        return view('SuperAdmin.employee.employeeProfile')->with(['user' => $user]);
    }

    #Role Page Method
    public function roles()
    {
        $roles = Role::all();
        return view('SuperAdmin.roles.roles')->with(['roles' => $roles]);
    }

    #validate and add new role subitted at Role Page Super Admin Access
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
                return response()->json(['success' => true]);
            }
        }
        return response()->json($validator->errors());
    }

    #delete role
    public function deleteRole(Request $request)
    {
        $role = Role::find($request->role);
        if($role->delete())
        {
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

    #update role details
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

            if($role->save())
            {
                return response()->json(['success' => true]);
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

    public function permissionFormValidation(Request $request)
    {
        $validator = Validator::make($request->All(),[
            'permission_name'          => 'required|min:3|max:20'
        ]);

        if($validator->passes())
        {

            if(Permission::create(['name' => $request->permission_name]))
            {
                return response()->json(['success' => true]);
            }
        }
        return response()->json($validator->errors());
    }

    public static function test()
    {
        return 'hello';
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

    public function lgu()
    {
        return view('SuperAdmin.lgu.lgu');
    }

    public function reports()
    {

    }


    /*
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
