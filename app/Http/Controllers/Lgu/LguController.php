<?php

namespace App\Http\Controllers\Lgu;

use App\ContactPerson;
use App\Http\Controllers\address\AddressController;
use App\Http\Controllers\CallCenter\CallCenterController;
use App\Http\Controllers\Reports\Reports;
use App\Http\Controllers\UserAgentController;
use App\Lgu;
use App\Models\CallCenter;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\User;

class LguController extends Controller
{
    private $activity, $address;
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
     * add new LGU
     * @param Request $request
     * @return Response
     * */
    public function addLgu(Request $request)
    {

        if(auth()->user()->getRoleNames()[0] == "super admin")
        {

            $validation = [
                'station_name'          => 'required|max:50',
                'department'            => 'required|max:50',
                'street_address'        => 'required:max:100',
                'region'                => 'required',
                'state'                 => 'required',
                'city'                  => 'required|max:60',
                'contactperson_name'    => 'required',
                'contactperson_no'      => 'required',
                'call_center'      => 'required',
            ];
            $callCenterValue = $request->call_center;
        }else{
            $user = User::find(Auth::user()->id)->callcenter;
            $callcenter_id = $user[0]->pivot->cc_id;

            $callCenterValue = $callcenter_id;
            $validation = [
                'station_name'          => 'required|max:50',
                'department'            => 'required|max:50',
                'street_address'        => 'required:max:100',
                'region'                => 'required',
                'state'                 => 'required',
                'city'                  => 'required|max:60',
                'contactperson_name'    => 'required',
                'contactperson_no'      => 'required'
            ];
        }

        $validator = Validator::make($request->all(),$validation);

        if($validator->passes())
        {
            $lgu = new Lgu;
            $lgu->call_center_id = $callCenterValue;
            $lgu->station_name = $request->station_name;
            $lgu->department = $request->department;
            $lgu->region = $request->region;
            $lgu->province = $request->state;
            $lgu->city = $request->city;
            $lgu->address = $request->street_address;
            $lgu->postalCode = $request->postal_code;

            if($lgu->save())
            {
                if(!empty($request->contactperson_name) && !empty($request->contactperson_no))
                {
                    $this->assignContactPerson($lgu->id, $request->contactperson_name, $request->contactperson_no);
                }

                /*Activity log*/
//                $action = "added a new LGU - Station Name: ".$request->station_name;
//                $action .= ", Department: ".$request->department;
//                $action .= ", Location: ".$request->street_address.", ".$this->address->get_city_name($request->city).", "
//                    .$this->address->get_province_name($request->state).", ".$this->address->getRegion($request->region);
//                $action .= ", with Contact Person: ".$request->contactperson_name.", Contact Person Number: ".$request->contactperson_no;
//
//                $action .= "  assigned to Call Center: ".CallCenter::find($callCenterValue)->name;

                $description = 'Added new LGU';

                $action = $this->device->userAgent();
                $action .='<table class="table table-bordered">';
                $action .= '<tr><td colspan="2">Action: '.$description.'</td></tr>';
                $action .= '<tr><td>Station Name</td><td>'.$request->station_name.'</td></tr>';
                $action .= '<tr><td>Department</td><td>'.$request->department.'</td></tr>';
                $action .= '<tr><td>Address</td><td>'.$request->street_address.'</td></tr>';
                $action .= '<tr><td>City</td><td>'.$this->address->get_city_name($request->city).'</td></tr>';
                $action .= '<tr><td>State</td><td>'.$this->address->get_province_name($request->state).'</td></tr>';
                $action .= '<tr><td>Region</td><td>'.$this->address->getRegion($request->region).'</td></tr>';
                $action .= '<tr><td>Postal Code</td><td>'.$request->postal_code.'</td></tr>';
                $action .= '<tr><td>Contact Person</td><td>'.$request->contactperson_name.'</td></tr>';
                $action .= '<tr><td>Contact Person Number</td><td>'.$request->contactperson_no.'</td></tr>';
                $action .= '<tr><td>Call Center</td><td>'.CallCenter::find($callCenterValue)->name.'</td></tr>';
                $action .= '</table>';

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
     * assign the contact person to an LGU
     * @param int $lgu_id
     * @param int $full_name
     * @param int $contactNo
     * @return void
     * */
    public function assignContactPerson($lgu_id, $full_name, $contactNo)
    {
        $contactPerson = new ContactPerson;
        $contactPerson->lgu_id = $lgu_id;
        $contactPerson->fullname = $full_name;
        $contactPerson->contactno = $contactNo;
        $contactPerson->save();
    }

    /**
     * retrieve LGU data
     * @param int $lgu_id
     * @return mixed
     * */
    public function lgu_profile($lgu_id)
    {
        $lguDetails = DB::table('lgus')
            ->leftJoin('contact_people','lgus.id','=','contact_people.lgu_id')
            ->select('lgus.*','contact_people.id as contactId','contact_people.fullname','contact_people.contactno')
            ->where('lgus.id','=',$lgu_id)->first();
        return view('lgu.lguProfile')->with([
            'lguDetails'    => $lguDetails,
        ]);
    }

    /**
     * date: Oct. 07, 2019
     * by: john kevin paunel
     * get the lgu data thru ID
     * @param int $lguId
     * @return Response
     * */
    public function lgu_data(Request $request)
    {
        $option = new CallCenterController;

        $lgu = DB::table('lgus')
            ->leftJoin("contact_people",'lgus.id','=','contact_people.lgu_id')
            ->select('lgus.*','contact_people.id as contact_id','contact_people.fullname','contact_people.contactno')
            ->where('lgus.id','=',$request->id)->first();

        /**
         * input all lgu data in one variable
         * @var $data
         * */
        $data = [
            "lguId"         => $lgu->id,
            "cc_id"         => $lgu->call_center_id,
            "stationName"   => $lgu->station_name,
            "department"    => $lgu->department,
            "region"        => $lgu->region,
            "province"      => $lgu->province,
            "city"          => $lgu->city,
            "address"       => $lgu->address,
            "contactId"     => $lgu->contact_id,
            "fullname"      => $lgu->fullname,
            "contactNo"     => $lgu->contactno,
            'postalCOde'    => $lgu->postalCode,
            'province_value'  => $option->getProvinces($lgu->region),
            'city_value'  => $option->getCities($lgu->province),
        ];

        return response()->json($data);
    }

    /**
     * update the LGU details
     * @param Request $request
     * @return Response
     * */
    public function update_lgu(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'edit_station_name'          => 'required|max:50',
            'edit_department'            => 'required|max:50',
            'edit_street_address'        => 'required:max:100',
            'edit_region'                => 'required',
            'edit_state'                 => 'required',
            'edit_city'                  => 'required|max:60',
            'edit_postal_code'           => 'required|max:60',
            'edit_contactperson_name'    => 'required',
            'edit_contactperson_no'      => 'required'
        ]);

        if($validator->passes())
        {

            /*check if the submitted input matches the old data*/
            $checkinput = DB::table('lgus')
                ->leftJoin("contact_people",'lgus.id','=','contact_people.lgu_id')
                ->select('lgus.*','contact_people.id as contact_id','contact_people.fullname','contact_people.contactno')
                ->where([
                    ['lgus.id','=',$request->lguId],
                    ['lgus.call_center_id','=',$request->ccId],
                    ['lgus.station_name','=',$request->edit_station_name],
                    ['lgus.department','=',$request->edit_department],
                    ['lgus.region','=',$request->edit_region],
                    ['lgus.province','=',$request->edit_state],
                    ['lgus.city','=',$request->edit_city],
                    ['lgus.address','=',$request->edit_street_address],
                    ['lgus.postalCode','=',$request->edit_postal_code],
                    ['contact_people.fullname','=',$request->edit_contactperson_name],
                    ['contact_people.contactno','=',$request->edit_contactperson_no],
                ])->count();

            if($checkinput < 1)
            {
                /*this will update the lgu Details*/
                $lgu = Lgu::find($request->lguId);
                $lgu->call_center_id = $request->ccId;
                $lgu->station_name = $request->edit_station_name;
                $lgu->department = $request->edit_department;
                $lgu->region = $request->edit_region;
                $lgu->province = $request->edit_state;
                $lgu->city = $request->edit_city;
                $lgu->address = $request->edit_street_address;
                $lgu->postalCode = $request->edit_postal_code;

                $lguDetails = DB::table('lgus')
                    ->leftJoin("contact_people",'lgus.id','=','contact_people.lgu_id')
                    ->select('lgus.*','contact_people.id as contact_id','contact_people.fullname','contact_people.contactno')
                    ->where('lgus.id','=',$request->lguId)->first();
//
                $lguId = $lguDetails->id;
                $stationname = $lguDetails->station_name;
                $department = $lguDetails->department;
                $region = $lguDetails->region;
                $state = $lguDetails->province;
                $city = $lguDetails->city;
                $address = $lguDetails->address;
                $postalCode = $lguDetails->postalCode;
                $contactPersonName = $lguDetails->fullname;
                $contactPersonNo = $lguDetails->contactno;
                $contactId = $lguDetails->contact_id;
                $callCenterId = $lguDetails->call_center_id;

                if($lgu->save())
                {
                    /*this will update the contact person details*/
                    $contactPerson = ContactPerson::find($request->contactId);
                    $contactPerson->fullname = $request->edit_contactperson_name;
                    $contactPerson->contactno = $request->edit_contactperson_no;

                    /*update details of contact person*/
                    $contactPerson->save();

                    /*display new data*/
                    $description = 'Updated LGU details';

                    $action = $this->device->userAgent();
                    $action .='<table class="table table-bordered">';
                    $action .= '<tr><td colspan="3">Action: '.$description.'</td></tr>';
                    $action .= '<tr><td></td><td>Previous</td><td>Updated</td></tr>';
                    $action .= '<tr><td colspan="3">Call Center ID: '.$request->ccId.'</td></tr>';
                    $action .= '<tr><td>Station Name</td><td>'.$stationname.'</td><td>'.$request->edit_station_name.'</td></tr>';
                    $action .= '<tr><td>Department</td><td>'.$department.'</td><td>'.$request->edit_department.'</td></tr>';
                    $action .= '<tr><td>Address</td><td>'.$address.'</td><td>'.$request->edit_street_address.'</td></tr>';
                    $action .= '<tr><td>City</td><td>'.$this->address->get_city_name($city).'</td><td>'.$this->address->get_city_name($request->edit_city).'</td></tr>';
                    $action .= '<tr><td>State</td><td>'.$this->address->get_province_name($state).'</td><td>'.$this->address->get_province_name($request->edit_state).'</td></tr>';
                    $action .= '<tr><td>Region</td><td>'.$this->address->getRegion($region).'</td><td>'.$this->address->getRegion($request->edit_region).'</td></tr>';
                    $action .= '<tr><td>Postal Code</td><td>'.$postalCode.'</td><td>'.$request->edit_postal_code.'</td></tr>';
                    $action .= '<tr><td>Contact Person</td><td>'.$contactPersonName.'</td><td>'.$request->edit_contactperson_name.'</td></tr>';
                    $action .= '<tr><td>Contact Person Number</td><td>'.$contactPersonNo.'</td><td>'.$request->edit_contactperson_no.'</td></tr>';
                    $action .= '<tr><td>Call Center</td><td>'.CallCenter::find($callCenterId)->name.'</td><td>'.CallCenter::find($request->ccId)->name.'</td></tr>';
                    $action .= '</table>';

                    $this->activity->activity_log($action, $description);

                    $message = ['success' => true];
                }else{
                    $message = ['success' => false];
                }
            }else{
                $message = ['success' => 'No changes occurred'];
            }

            return response()->json($message);
        }

        return response()->json($validator->errors());
    }

    /**
     * fetch LGU name for delete display
     * @param Request $request
     * @return mixed
     * */
    public function display_delete_lgu(Request $request)
    {
        return Lgu::find($request->id);
    }

    /**
     * soft delete LGU
     * @param Request $request
     * @return Response
     * */
    public function delete_lgu(Request $request)
    {
        $lgu = Lgu::find($request->lgu_delete_id);


        if($lgu->delete())
        {
            $description = "Deleted LGU";
            $action = $this->device->userAgent();
            $action .= '<table class="table table-bordered">';
            $action .= '<tr><td colspan="2">Action: '.$description.'</td></tr>';
            $action .= '<tr><td>LGU ID</td><td>'.$request->lgu_delete_id.'</td></tr>';
            $action .= '<tr><td>Station Name</td><td>'.$lgu->station_name.'</td></tr>';
            $action .= '</table>';

            $this->activity->activity_log($action, $description);
            $message = ["success" => true];
        }else{
            $message = ["success" => false];
        }
        return response()->json($message);
    }
}
