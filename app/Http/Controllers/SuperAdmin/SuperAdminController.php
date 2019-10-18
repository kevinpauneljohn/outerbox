<?php

namespace App\Http\Controllers\SuperAdmin;

use App\activity;
use App\address\Region;
use App\Http\Controllers\address\AddressController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\TimeController;
use App\Http\Controllers\UserAgentController;
use App\Lgu;
use App\Models\Lead;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use App\Models\CallCenter;
use App\Http\Controllers\Reports\Reports;

use App\Repositories\Lead\LeadRepositoryContract;

use LaravelDaily\LaravelCharts\Classes\LaravelChart;

class SuperAdminController extends Controller
{
    /**
     * date: oct 05, 2019
     * by: john kevin paunel
     * activity logs variable
     * @var $activity
     * */
    private $activity;


    // Added Oct. 10, 2019 by Jovito Pangan
    // Lead Count and For report leads
    protected $leads;

    /**
     * @var $device
     * */
    private $device;

    /**
     * date: oct. 05, 2019
     * by: john kevin paunel
     * this will initialized the report controller
     * @return void
     * */
    public function __construct(LeadRepositoryContract $leads)
    {
        $this->leads = $leads;
        $this->activity = new Reports;
        $this->device = new UserAgentController;
    }

    public function dashboard()
    {
               /**
         * Statistics for leads this month.
         */
        // $leadCompletedThisMonth = $this->leads->completedLeadsThisMonth();
        // $completedLeadsMonthly = $this->leads->completedLeadsThisMonth();
        // $createdLeadsMonthly   = $this->leads->createdLeadsMonthly();

        $chart_options = [
            'chart_title' => 'Users by months',
            'report_type' => 'group_by_date',
            'model' => 'App\User',
            'group_by_field' => 'created_at',
            'group_by_period' => 'month',
            'chart_type' => 'bar',
        ];
        $chart1 = new LaravelChart($chart_options);

        $chart_option_tickets = [
            'chart_title' => 'Call Centers by months',
            'report_type' => 'group_by_date',
            'model' => 'App\Ticket',
            'group_by_field' => 'created_at',
            'group_by_period' => 'day',
            'chart_type' => 'line',
        ];
        $chart2 = new LaravelChart($chart_option_tickets);

        return view('SuperAdmin.dashboard', compact('chart1', 'chart2'));
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
                ['roles.name','!=','Lgu'],
                ['roles.name','!=','super admin'],
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
        $role = auth()->user()->getRoleNames()[0];
        if(User::find($id) != null)
        {
            if(CallCenter::all()->count() > 0)
            {
                $user = User::find($id);
                $activities = activity::where('user_id',$id)->get();
                return view('SuperAdmin.employee.employeeProfile')->with([
                    'user'          => $user,
                    'activities'    => $activities,
                    "dateTime"          => new TimeController,
                    "roles"             => new RolesController,
                    "roleList"          => Role::where('name','!=','Lgu')->get(),
                    "callCenterUser"        => User::find($id)->callcenter()->first(),
                    "active"            => User::where([['id','=',$id],['active','=',1]]),
                    "userDetails"            => User::where([['id','=',$id]]),
                ]);
            }else{

                if($role == "super admin")
                {
                    return redirect(url('/super-admin/dashboard'));
                }elseif($role == "admin"){
                    return redirect(url('/dashboard'));
                }
            }
        }else{
            if($role == "super admin")
            {
                return redirect(url('/super-admin/dashboard'));
            }elseif($role == "admin"){
                return redirect(url('/dashboard'));
            }
        }

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
                $action = $this->device->userAgent();
                $action .= "<b>Added a new Role: </b>".$request->name;
                $action .= '<tr><td><b>Role Name</b></td><td>'.$request->name.'</td></tr>';
                $action .= '</table>';
                $description = "Added a new role";
                $this->activity->activity_log($action, $description);

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
            /*activity log*/
            $description = "Deleted a role";
            $action = $this->device->userAgent();
            $action .= '<tr><td><b>Role Name</b></td><td>'.$role->name.'</td></tr>';
            $action .= '<tr><td><b>Role ID</b></td><td>'.$request->role.'</td></tr>';
            $action .= '</table>';
            $this->activity->activity_log($action, $description);
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

                if($role->save())
                {
                    $action = $this->device->userAgent();
                    $action .= '<table class="table table-bordered">';
                    $action .= '<tr><td></td><td><b>Previous</b></td><td><b>Updated</b></td></tr>';
                    $action .= '<tr><td><b>Role name</b></td><td>'.$oldRole->name.'</td><td>'.$request->edit_name.'</td></tr>';
                    $action .= '<tr><td><b>Description</b></td><td>'.$oldRole->description.'</td><td>'.$request->edit_description.'</td></tr>';
                    $action .= '</table>';
                    $description = "Updated a role";
                    $this->activity->activity_log($action, $description);
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
                /*$action = "added new permission: ".$request->permission_name;*/
                $description = 'Added new permission';

                $action = $this->device->userAgent();
                $action .= '<table class="table table-bordered">';
                $action .= '<tr><td><b>Action:</b> '.$description.'</td></tr>';
                $action .= '<tr><td><b>Permission Name:</b></td><td>'.$request->permission_name.'</td></tr>';
                $action .= '<table/>';
                $this->activity->activity_log($action,$description);
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

    /**
     * call center profile view page
     * @param int $id
     * @return mixed
     * */
    public function callCenterProfile($id)
    {

        $callCenter = CallCenter::find($id);

        $employee = DB::table('users')
            ->leftJoin('callcenterdetails', 'users.id', '=', 'callcenterdetails.user_id')
            ->leftJoin('model_has_roles','users.id','=','model_has_roles.model_id')
            ->leftJoin('roles','model_has_roles.role_id','=','roles.id')
            ->select('users.*','roles.name as role_name')
            ->where([
                ['roles.name','!=','super admin'],
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
            ->leftJoin('users','contact_people.user_id','=','users.id')
            ->select('users.firstname','users.lastname','users.username',
                'lgus.id as lgu_id','lgus.station_name','lgus.department','lgus.created_at','lgus.region','lgus.province','lgus.city','lgus.address',
                'call_centers.id as cc_id','contact_people.contactno')
            ->where('lgus.deleted_at','=',null
            );

        /*$cityname = new AddressController();

        foreach ($lgus->get() as $lgu)
        {
            echo $cityname->get_city_name($lgu->city);
        }*/
//        return $lgus->get();
        return view('SuperAdmin.lgu.lgu')->with([
            "lgus"          => $lgus,
            "regions"       => $regions,
            "callCenters"    => $callCenter,
            "address"       => new AddressController,
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
        $dateTime = new TimeController;
        return view("SuperAdmin.Activity.activity")->with([
            "activities"        => $activities,
            "dateTime"          => $dateTime,
            "roles"             => new RolesController,
        ]);
    }

    /////////////////////////////// -------------- REPORTS MODULE --------------- ////////////////////////////
    /**
     * date: Oct. 10, 2019
     * by: Jovito Pangan
     * Performance Evaluation Report
     * @return object
     */
    public function performance_eval(){
        return view("SuperAdmin.Reports.performance_eval");
    }

    /**
     * date: Oct. 10, 2019
     * by: Jovito Pangan
     * User management Report
     * @return object
     */
    public function user_management(){
        return view("SuperAdmin.Reports.user_management");
    }

    /**
     * date: Oct. 10, 2019
     * by: Jovito Pangan
     * Forecast Report
     * @return object
     */
    public function forecast(){
        return view("SuperAdmin.Reports.forecast");
    }


    /////////////////////////////// -------------- END OF REPORTS MODULE --------------- ////////////////////////////

    /**
     * for mobile app
     * display of call center list
     * @return object
     * */
    public function show_call_center_list()
    {
        return CallCenter::all()->pluck('postalcode');
    }

    /**
     * by: john kevin paunel
     * show list of LGUs
     * @return  object
     * */
    public function show_lgu_list()
    {
        return Lgu::all();
    }

    /**
     * Oct. 18, 2019
     * @author john kevin paunel
     * announcement view page of super admin
     * url: /announcement
     * @return mixed
     * */
    public function announcement()
    {
        return view('SuperAdmin.announcement');
    }
}
