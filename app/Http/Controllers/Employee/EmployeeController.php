<?php

namespace App\Http\Controllers\Employee;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
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
                $message = ['success' => true];
            }else{
                $message = ['success' => false];
            }

            return response()->json($message);
        }

        return response()->json($validator->errors());
    }

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

            if($user->save())
            {
                if(!empty($request->edit_callcenter))
                {
                    $this->updateUserAssignmentToCC($request->user_value,$request->edit_callcenter);
                }
                $message = ['success' => true];
            }else{
                $message = ['success' => false];
            }

            return response()->json($message);
        }

        return response()->json($validator->errors());
    }

    public function deleteEmployee(Request $request)
    {
        $user = User::find($request->user_delete)->delete();
        $message = ($user) ? ['success' => true] : ['success' => false];
        return response()->json($message);
    }
}
