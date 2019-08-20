<?php

namespace App\Http\Controllers\Lgu;

use App\Lgu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\User;

class LguController extends Controller
{
    public function addLgu(Request $request)
    {
        $user = User::find(Auth::user()->id)->callcenter;
        $callcenter_id = $user[0]->pivot->cc_id;
        $validator = Validator::make($request->all(),[
            'station_name'      => 'required|max:50',
            'department'        => 'required|max:50',
            'street_address'    => 'required:max:100',
            'region'            => 'required',
            'state'             => 'required',
            'city'              => 'required|max:60'
        ]);

        if($validator->passes())
        {
            $lgu = new Lgu;
            $lgu->call_center_id = $callcenter_id;
            $lgu->station_name = $request->station_name;
            $lgu->department = $request->department;
            $lgu->region = $request->region;
            $lgu->province = $request->state;
            $lgu->city = $request->city;
            $lgu->address = $request->street_address;

            $message = ($lgu->save()) ? ['success' => true] : ['success' => false];
            return response()->json($message);
        }

        return response()->json($validator->errors());
    }
}
