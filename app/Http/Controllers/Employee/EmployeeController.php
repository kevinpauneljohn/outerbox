<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Reports\Reports;
use App\Models\CallCenter;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public $activity;

    public function __construct()
    {
        $this->activity  = new Reports;
    }

    /**
     * add new employee
     * @param Request $request
     * @return mixed
    */
    public function addEmployee(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'firstname'     => 'required|min:2|max:50',
            'lastname'      => 'required|min:2|max:50',
            'email'         => 'required|email|unique:users,email',
            'username'      => 'required|unique:users,username',
            'password'      => 'required|min:3|max:50|confirmed',
            'role'          => 'required',
            'callcenter'          => 'required'
        ]);
//
        if($validator->passes())
        {
            $user = new User;
            $user->firstname = $request->firstname;
            $user->middlename = $request->middlename;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->username = $request->username;
            $user->password = bcrypt($request->password);
            $user->active = 0;
            $user->assignRole($request->role);

            if($user->save())
            {
                if(!empty($request->callcenter) > 0)
                {
                    $this->assignUserTocallCenter($user->id,$request->callcenter);
                }

                /**
                 * create activity logs for adding employee of super admin
                 * @var $action
                 * */
                $action = '<table class="table table-bordered">';
                $action .= '<tr><td>Action: Added New Employee</td><td></td></tr>';
                $action .= '<tr>';
                $action .= '<td>Name: </td><td>'.$request->firstname;
                $action .= (!empty($request->middlename)) ? $request->middlename : ' ';
                $action .= $request->lastname.'</td>';
                $action .= '</tr>';
                $action .= '<tr>';
                $action .= '<td>Email: </td><td>'.$request->email.'<td/>';
                $action .= '</tr><tr>';
                $action .= '<td>Username: </td><td>'.$request->username.'<td/>';
                $action .= '</tr><tr>';
                $action .= '<td>Role: </td><td>'.$request->role.'<td/>';
                $action .= '</tr><tr>';
                $action .= '<td>Call Center: </td><td>'.CallCenter::find($request->callcenter)->name.'<td/>';
                $action .= '</tr></table>';

                /**
                 * @var $description
                 * */
                $description = "Added New Employee";
                $this->activity->activity_log($action, $description);

                $message = ['success' => true];
            }else{
                $message = ['success' => false];
            }

            return response()->json($message);
        }

        return response()->json($validator->errors());
    }

    /**
     * assign the user to a call center
     * @param int $userID
     * @param int $callCenterId
     * @return void
     * */
    public function assignUserTocallCenter($userID, $callCenterId)
    {
        $time = Carbon::now();
        DB::table('callcenterdetails')
            ->insert([
                'cc_id'         => $callCenterId,
                'user_id'       => $userID,
                'created_at'    => $time,
                'updated_at'    => $time
            ]);
    }

    /**
     * update the user assignment to a new call center
     * @param int $userID
     * @param int $callCenterId
     * @return void
     * */
    public function updateUserAssignmentToCC($userID, $callCenterId)
    {
        $time = Carbon::now();
        DB::table('callcenterdetails')
            ->where('user_id','=',$userID)
            ->update([
                'cc_id'         => $callCenterId,
                'created_at'    => $time,
                'updated_at'    => $time
            ]);
    }

    public function getEmployeeDetails(Request $request)
    {
        $users = DB::table('users')
        ->leftJoin('callcenterdetails', 'users.id', '=', 'callcenterdetails.user_id')
        ->leftJoin('call_centers','callcenterdetails.cc_id','=','call_centers.id')
        ->leftJoin('model_has_roles','users.id','=','model_has_roles.model_id')
        ->leftJoin('roles','model_has_roles.role_id','=','roles.id')
        ->select(
            'users.id','users.firstname','users.middlename','users.lastname','users.email','users.username','roles.name as role_name','callcenterdetails.cc_id as cc_id','call_centers.name as cc_name')
        ->where('users.id',$request->id)
        ->get();

        return json_decode($users);
    }


    /**
     * Update the existing employee details
     * @param Request $request
     * @return Response
     * */
    public function updateEmployeeDetails(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'edit_firstname'     => 'required|min:2|max:50',
            'edit_lastname'      => 'required|min:2|max:50',
            'edit_email'         => 'required',
            'edit_role'          => 'required',
            'edit_callcenter'          => 'required'
        ]);

        if($validator->passes())
        {
            $user = User::find($request->user_value);
            $user->firstname = $request->edit_firstname;
            $user->middlename = $request->edit_middlename;
            $user->lastname = $request->edit_lastname;
            $user->email = $request->edit_email;
            $user->removeRole($request->old_role);
            $user->assignRole($request->edit_role);

            /**
             * check if there is changes on the submitted inputs
             * @var $checkInput
             * */
            $checkInput = DB::table('users')
                ->leftJoin('callcenterdetails','users.id','=','callcenterdetails.user_id')
                ->leftJoin('model_has_roles','users.id','=','model_has_roles.model_id')
                ->leftJoin('roles','model_has_roles.role_id','=','roles.id')
                ->select('users.*','roles.name as role_name','callcenterdetails.*')
                ->where([
                    ['users.firstname','=',$request->edit_firstname],
                    ['users.middlename','=',!empty($request->edit_middlename) ? $request->edit_middlename : null],
                    ['users.lastname','=',$request->edit_lastname],
                    ['users.email','=',$request->edit_email],
                    ['roles.name','=',$request->old_role],
                    ['callcenterdetails.cc_id','=',$request->edit_callcenter],

                ]);


            if($checkInput->count() < 1)
            {

                /**
                 * get the current employee details
                 * @var int
                 * */
                $prevInput = DB::table('users')
                    ->leftJoin('callcenterdetails','users.id','=','callcenterdetails.user_id')
                    ->leftJoin('model_has_roles','users.id','=','model_has_roles.model_id')
                    ->leftJoin('roles','model_has_roles.role_id','=','roles.id')
                    ->select('users.*','roles.name as role_name','callcenterdetails.*')
                    ->where('users.id','=',$request->user_value)
                    ->first();

//                $previousAction = '<table>';
//                $previousAction = '<tr><td>First Name:</td><td>'.$prevInput->firstname.'</td></tr>';
//                $previousAction .= '<tr><td>Middle Name: </td><td>'.$prevInput->middlename.'</td></tr>';
//                $previousAction .= '<tr><td>Las</td>'.$prevInput->lastname.'</tr>';
//                $previousAction .= ", Email: ".$prevInput->email;
//                $previousAction .= ", Role: ".$request->old_role;
//                $previousAction .= ", Call Center: ".CallCenter::find($prevInput->cc_id)->name;
//                $previousAction .= '</table>';

                if($user->save())
                {
                    if(!empty($request->edit_callcenter))
                    {
                        $this->updateUserAssignmentToCC($request->user_value,$request->edit_callcenter);
                    }
                    $message = ['success' => true];

                    $action = '<table class="table table-bordered">';
                    $action .= '<thead><tr><td></td><td>Previous</td><td>Updated</td></tr></thead>';
                    $action .= '<tr><td>First Name</td><td>'.$prevInput->firstname.'</td><td>'.$request->edit_firstname.'</td></tr>';
                    $action .= '<tr><td>Middle Name</td><td>'.$prevInput->middlename.'</td><td>'.$request->edit_middlename.'</td></tr>';
                    $action .= '<tr><td>Last Name</td><td>'.$prevInput->lastname.'</td><td>'.$request->edit_lastname.'</td></tr>';
                    $action .= '<tr><td>Email</td><td>'.$prevInput->email.'</td><td>'.$request->edit_email.'</td></tr>';
                    $action .= '<tr><td>Role</td><td>'.$request->old_role.'</td><td>'.$request->edit_role.'</td></tr>';
                    $action .= '<tr><td>Call Center</td><td>'.CallCenter::find($prevInput->cc_id)->name.'</td><td>'.CallCenter::find($request->edit_callcenter)->name.'</td></tr>';
                    $action .= '</table>';
                    /*$this->activity->activity_log($previousAction." ".$action, "Updated User");*/
                    $this->activity->activity_log($action, "Updated User");
                }else{
                    $message = ['success' => false];
                }

                return response()->json($message);
            }else{
                return ['success' => 'No changes occurred'];
            }


        }

        return response()->json($validator->errors());
    }

    /**
     * Soft Deleting employee
     * @param Request $request
     * @return Response
     * */
    public function deleteEmployee(Request $request)
    {
        $user = User::find($request->user_delete);
        $action = '<table class="table table-bordered">';
        $action .= '<tr><td colspan="2">Action: Deleted User</td></tr>';
        $action .= '<tr><td>User ID: </td><td>'.$user->id.'</td></tr><tr><td>Username</td><td>'.$user->username.'</td></tr>';
        $action .= '</table>';

        $description = "Deleted user";

        $message = ($user->delete()) ? ['success' => true] : ['success' => false];
        $this->activity->activity_log($action, $description);
        return response()->json($message);
    }
}
