<?php

namespace App\Http\Controllers\CallCenter;

use App\address\Municipality;
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
            'city'             => 'required|unique:call_centers,city',
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
        $data = [
            'province_value'  => $this->getProvinces($callCenter->region),
            'city_value'  => $this->getCities($callCenter->state)
        ];
        $callCenter = json_decode($callCenter,true);
        $json = array_merge($callCenter,$data);

        return $json;
    }

    private function getProvinces($regCode)
    {
            $provinces = Province::where('regCode',$regCode)->get();

            $option = "";
            foreach ($provinces as $province){
                $option .= '<option value="'.$province->provCode.'">'.$province->provDesc.'</option>';
            }
            return $option;
    }

    public function getCities($proCode)
    {
        $cities = Municipality::where('provCode',$proCode)->get();
        $option = "";
        foreach ($cities as $city){
            $option .= '<option value="'.$city->citymunCode.'">'.$city->citymunDesc.'</option>';
        }
        return $option;
    }

    public function updateCallCenterDetails(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'update_callcenter'         => 'required',
            'update_street_address'     => 'required',
            'update_region'             => 'required',
            'update_state'              => 'required',
            'update_postal_code'        => 'required',
            'update_city'               => 'required|unique:call_centers,city',
        ]);

        if($validator->passes())
        {
            $callCenter = CallCenter::find($request->callcenter_value);
            $callCenter->name = $request->update_callcenter;
            $callCenter->region = $request->update_region;
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

    //count number of agents per call center
    public static function totalEmployees($callcenter_id)
    {
        $employees = CallCenter::find($callcenter_id)->users->count();
        return $employees;
    }

}
