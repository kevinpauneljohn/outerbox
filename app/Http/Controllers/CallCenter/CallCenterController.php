<?php

namespace App\Http\Controllers\CallCenter;

use App\address\Province;
use App\Models\CallCenter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CallCenterController extends Controller
{

    #this method will create a new call center account
    public function addNewCallCenter(Request $request)
    {
        $validator = Validator::make($request->All(),[
            'callcenter'       => 'required|min:1|max:20',
            'street_address'   => 'required|max:100',
            'region'           => 'required',
            'state'            => 'required',
            'postal_code'      => 'required',
            'city'             => 'required',
        ]);

        if($validator->passes())
        {
            $callCenter = new CallCenter;
            $callCenter->name = $request->callcenter;
            $callCenter->region = $request->region;
            $callCenter->street = $request->street_address;
            $callCenter->state = $request->state;
            $callCenter->postalcode = $request->postal_code;
            $callCenter->city = $request->city;

            $message = ($callCenter->save()) ? ['success' => true] : ['success' =>false];
            return response()->json($message);
        }
        return response()->json($validator->errors());
    }

    public function getCallCenterDetails(Request $request)
    {
        $callCenter = CallCenter::find($request->id);
        $data = ['prov code'=> $this->getCity($callCenter->state)];
        $callCenter = json_decode($callCenter,true);
        $json = array_merge($callCenter,$data);

        return $json;
    }

    private function getCity($provCOde)
    {
        $province = Province::where('provCode',$provCOde)->first();
        return $province->provDesc;
    }

    public function updateCallCenterDetails(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'update_callcenter'         => 'required|min:3|max:30',
            'update_street_address'     => 'required|min:3|max:50',
            'update_state'              => 'required|min:2|max:50',
            'update_postal_code'        => 'required|min:2|max:10',
            'update_city'               => 'required|min:2|max:30',
        ]);

        if($validator->passes())
        {
            $callCenter = CallCenter::find($request->callcenter_value);
            $callCenter->name = $request->update_callcenter;
            $callCenter->street = $request->update_street_address;
            $callCenter->state = $request->update_state;
            $callCenter->postalcode = $request->update_postal_code;
            $callCenter->city = $request->update_city;

            $message = ($callCenter->save()) ? ['success' => true] : ['success' => false];
            return response()->json($message);
        }

        return response()->json($validator->errors());
    }

    public function deleteCallCenter(Request $request)
    {
        $callCenter = CallCenter::find($request->call_center_delete_id);
        $message = ($callCenter->delete()) ? ['success' => true] : ['success' => false];

        return response()->json($message);
    }

}
