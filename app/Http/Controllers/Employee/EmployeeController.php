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

                $action = 'added new employee - name: '.$request->firstname;
                $action .= (!empty($request->middlename)) ? $request->middlename : ' ';
                $action .= $request->lastname;
                $action .= ', email: '.$request->email;
                $action .= ', username: '.$request->username;
                $action .= ', role: '.$request->role;
                $action .= ' and assigned to Call Center: '.CallCenter::find($request->callcenter)->name;


                $this->activity->activity_log($action);

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

                $previousAction = "updated employee details from - First Name: ".$prevInput->firstname;
                $previousAction .= ", Middle Name: ".$prevInput->middlename;
                $previousAction .= ", Last Name: ".$prevInput->lastname;
                $previousAction .= ", Email: ".$prevInput->email;
                $previousAction .= ", Role: ".$request->old_role;
                $previousAction .= ", Call Center: ".CallCenter::find($prevInput->cc_id)->name;

                if($user->save())
                {
                    if(!empty($request->edit_callcenter))
                    {
                        $this->updateUserAssignmentToCC($request->user_value,$request->edit_callcenter);
                    }
                    $message = ['success' => true];

                    $action = "to - First Name: ".$request->edit_firstname;
                    $action .= ", Middle Name: ".$request->edit_middlename;
                    $action .= ", Last Name: ".$request->edit_lastname;
                    $action .= ", Email: ".$request->edit_email;
                    $action .= ", Role: ".$request->edit_role;
                    $action .= ", Call Center: ".CallCenter::find($request->edit_callcenter)->name;
                    $this->activity->activity_log($previousAction." ".$action);
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
        $action = "deleted ".$user->username." with user id: ".$user->id;

        $message = ($user->delete()) ? ['success' => true] : ['success' => false];
        $this->activity->activity_log($action);
        return response()->json($message);
    }
}
