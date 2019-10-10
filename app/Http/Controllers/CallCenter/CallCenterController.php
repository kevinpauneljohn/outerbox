<?php

namespace App\Http\Controllers\CallCenter;

use App\address\Municipality;
use App\address\Province;
use App\Http\Controllers\address\AddressController;
use App\Http\Controllers\Reports\Reports;
use App\Http\Controllers\UserAgentController;
use App\Models\CallCenter;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CallCenterController extends Controller
{

    public $activity, $address;
    /**
     * @var $device
     * */
    private $device;

    public function __construct()
    {
        $this->activity = new Reports;
        $this->address = new AddressController;
        $this->device = new UserAgentController;
    }

    /**
     * this method will create a new call center account
     * @param Request $request
     * @return Response
     * */
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

            $description = "Added Call Center";

            $action = $this->device->userAgent();
            $action .= '<table class="table table-bordered">';
            $action .= '<tr><td colspan="2">Action: '.$description.'</td></tr>';
            $action .= '<tr><td>Call Center Name</td><td>'.$request->callcenter.'</td></tr>';
            $action .= '<tr><td>Address</td><td>'.$request->postal_code.'</td></tr>';
            $action .= '<tr><td>City</td><td>'.$this->address->get_city_name($request->city).'</td></tr>';
            $action .= '<tr><td>State</td><td>'.$this->address->get_province_name($request->state).'</td></tr>';
            $action .= '<tr><td>Region</td><td>'.$this->address->getRegion($request->region).'</td></tr>';
            $action .= '<tr><td>Postal Code</td><td>'.$request->postal_code.'</td></tr>';
            $action .= '</table>';

            $this->activity->activity_log($action, $description);

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

    public function getProvinces($regCode)
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
            'update_city'               => 'required',
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


            /**
             * check if the submitted inputs matches the existing
             * */
            $checkinput = DB::table("call_centers")->where([
                ['name','=',$request->update_callcenter],
                ['region','=',$request->update_region],
                ['street','=',$request->update_street_address],
                ['state','=',$request->update_state],
                ['postalcode','=',$request->update_postal_code],
                ['city','=',$request->update_city]
            ])->count();

            if($checkinput < 1)
            {
                $callCenterDetails = CallCenter::find($request->callcenter_value);
//                $previousAction = "Name: ".$callCenterDetails->name." location: ".$callCenterDetails->street.", "
//                    .$this->address->get_city_name($callCenterDetails->city).", "
//                    .$this->address->get_province_name($callCenterDetails->state).", ".$this->address->getRegion($callCenterDetails->region)." ".$callCenterDetails->postalcode;
                $message = ($callCenter->save()) ? ['success' => true] : ['success' => false];

//                $action = "updated the Call Center Details from ".$previousAction." to Name: ".$request->update_callcenter." location: ".$request->update_street_address.", ".
//                    $this->address->get_city_name($request->update_city).", ".$this->address->get_province_name($request->update_state).", "
//                    .$this->address->getRegion($request->update_region)." ".$request->update_postal_code;

                $description = 'Updated call center';
                $action = $this->device->userAgent();
                $action .= '<table class="table table-bordered">';
                $action .= '<tr><td colspan="3">Action: '.$description.'</td></tr>';
                $action .= '<tr><td colspan="3">Call Center ID: '.$request->callcenter_value.'</td></tr>';
                $action .= '<tr><td></td><td>Previous</td><td>Updated</td></tr>';
                $action .= '<tr><td>Call Center Name: </td><td>'.$callCenterDetails->name.'</td><td>'.$request->update_callcenter.'</td></tr>';
                $action .= '<tr><td>Address</td><td>'.$callCenterDetails->street.'</td><td>'.$request->update_street_address.'</td></tr>';
                $action .= '<tr><td>City</td><td>'.$this->address->get_city_name($callCenterDetails->city).'</td><td>'.$this->address->get_city_name($request->update_city).'</td></tr>';
                $action .= '<tr><td>State</td><td>'.$this->address->get_province_name($callCenterDetails->state).'</td><td>'.$this->address->get_province_name($request->update_state).'</td></tr>';
                $action .= '<tr><td>Region</td><td>'.$this->address->getRegion($callCenterDetails->region).'</td><td>'.$this->address->getRegion($request->update_region).'</td></tr>';
                $action .= '<tr><td>Postal Code</td><td>'.$callCenterDetails->postalcode.'</td><td>'.$request->update_postal_code.'</td></tr>';
                $action .= '</table>';

                $this->activity->activity_log($action, $description);

                return response()->json($message);
            }else{
                return ['success' => "No Changes Occurred"];
            }

        }

        return response()->json($validator->errors());
    }

    public function deleteCallCenter(Request $request)
    {
        $callCenter = CallCenter::find($request->call_center_delete_id);
//        $previousAction = "Name: ".$callCenter->name." location: "
//            .$callCenter->street.", ".$this->address->get_city_name($callCenter->city).", "
//            .$this->address->get_province_name($callCenter->state).", "
//            .$this->address->getRegion($callCenter->region)." ".$callCenter->postalcode;

        $description = "Deleted Call Center";

        $action = $this->device->userAgent();
        $action .= '<table class="table table-bordered">';
        $action .= '<tr><td colspan="2">Action :'.$description.'</td></tr>';
        $action .= '<tr><td>Call Center ID: '.$request->call_center_delete_id.'</td></tr>';
        $action .= '<tr><td>Name</td><td>'.$callCenter->name.'</td></tr>';
        $action .= '<tr><td>Address</td><td>'.$callCenter->street.'</td></tr>';
        $action .= '<tr><td>City</td><td>'.$this->address->get_city_name($callCenter->city).'</td></tr>';
        $action .= '<tr><td>State</td><td>'.$this->address->get_province_name($callCenter->state).'</td></tr>';
        $action .= '<tr><td>Region</td><td>'.$this->address->getRegion($callCenter->region).'</td></tr>';
        $action .= '<tr><td>Postal Code</td><td>'.$callCenter->postalcode.'</td></tr>';
        $action .= '</table>';

        $message = ($callCenter->delete()) ? ['success' => true] : ['success' => false];

        $this->activity->activity_log($action, $description);
        return response()->json($message);
    }

    //count number of agents per call center
    public static function totalEmployees($callcenter_id)
    {
        $employees = CallCenter::find($callcenter_id)->users->count();
        return $employees;
    }

}
