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
            'role'          => 'required'
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
}
