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
use Spatie\Permission\Models\Role;

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
                'contactperson_fname'    => 'required',
                'contactperson_lname'    => 'required',
                'contactperson_no'      => 'required',
                'contactperson_uname'   => 'required|unique:users,username',
                'password'              => 'required|min:3|max:50|confirmed',
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
                'contactperson_fname'    => 'required',
                'contactperson_lname'    => 'required',
                'contactperson_no'      => 'required',
                'contactperson_uname'   => 'required|unique:users,username',
                'password'              => 'required|min:3|max:50|confirmed',
            ];
        }

        $validator = Validator::make($request->all(),$validation);

        if($validator->passes())
        {
            /**
             * @author john kevin paunel
             * oct 14, 2019
             * this will check if Lgu role name is existing and will return 0 if it is not
             * @var object $role
             * */
            $role = Role::where([
                ['name','=','Lgu'],
                ['deleted_at','=',null]
            ]);

            if($role->count() > 0)
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
                    $contactPerson = new User;
                    $contactPerson->firstname = $request->contactperson_fname;
                    $contactPerson->lastname = $request->contactperson_lname;
                    $contactPerson->username = $request->contactperson_uname;
                    $contactPerson->password = bcrypt($request->password);
                    $contactPerson->active = 0;
                    $contactPerson->assignRole('Lgu');

                    $contactPerson->save();
                    $this->assignContactPerson($lgu->id, $contactPerson->id, $request->contactperson_no);


                    /**
                     * @author john kevin paunel
                     * @var $description
                     * */

                    $description = 'Added new LGU';

                    /**
                     * @author john kevin paunel
                     * oct 14, 2019
                     * @var $action
                     * */
                    $action = $this->device->userAgent();
                    $action .='<table class="table table-bordered">';
                    $action .= '<tr><td colspan="2"><b>Action:</b> '.$description.'</td></tr>';
                    $action .= '<tr><td><b>Station Name</b></td><td>'.$request->station_name.'</td></tr>';
                    $action .= '<tr><td><b>Department</b></td><td>'.$request->department.'</td></tr>';
                    $action .= '<tr><td><b>Address</b></td><td>'.$request->street_address.'</td></tr>';
                    $action .= '<tr><td><b>City</b></td><td>'.$this->address->get_city_name($request->city).'</td></tr>';
                    $action .= '<tr><td><b>State</b></td><td>'.$this->address->get_province_name($request->state).'</td></tr>';
                    $action .= '<tr><td><b>Region</b></td><td>'.$this->address->getRegion($request->region).'</td></tr>';
                    $action .= '<tr><td><b>Postal Code</b></td><td>'.$request->postal_code.'</td></tr>';
                    $action .= '<tr><td><b>Contact Person</b></td><td>'.$request->contactperson_fname.' '.$request->contactperson_lname.'</td></tr>';
                    $action .= '<tr><td><b>Contact Person Username</b></td><td>'.$request->contactperson_uname.'</td></tr>';
                    $action .= '<tr><td><b>Contact Person Number</b></td><td>'.$request->contactperson_no.'</td></tr>';
                    $action .= '<tr><td><b>Call Center</b></td><td>'.CallCenter::find($callCenterValue)->name.'</td></tr>';
                    $action .= '</table>';

                    $this->activity->activity_log($action, $description);
                    $message = ['success' => true];
                }else{
                    $message = ['success' => false];
                }

                return response()->json($message);
            }else{
                /**
                 * @author john kevin paunel
                 * oct 14, 2019
                 * this will return error message if Lgu role name is non-existing
                 * */
                return response()->json(['success' => false, 'message' => 'Please Create Lgu role name first']);
            }
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
    public function assignContactPerson($lgu_id, $user_id, $contactNo)
    {
        $contactPerson = new ContactPerson;
        $contactPerson->lgu_id = $lgu_id;
        $contactPerson->user_id = $user_id;
        $contactPerson->contactno = $contactNo;
        $contactPerson->save();
    }

    /**
     * @author john kevin paunel
     * retrieve LGU data
     * route /agent/lgu/profile/{id}
     * controller: Lgu\LguController
     * method lgu_profile
     * @param int $lgu_id
     * @return mixed
     * */
    public function lgu_profile($lgu_id)
    {
        $lguDetails = DB::table('lgus')
            ->leftJoin('contact_people','lgus.id','=','contact_people.lgu_id')
            ->leftJoin('users','contact_people.user_id','=','users.id')
            ->select('lgus.*','contact_people.id as contactId','contact_people.contactno','users.firstname','users.lastname')
            ->where('lgus.id','=',$lgu_id)->first();
        return view('lgu.lguProfile')->with([
            'lguDetails'    => $lguDetails,
        ]);
    }

    /**
     * date: Oct. 07, 2019
     * @author john kevin paunel
     * get the lgu data thru ID
     * @param Request $request
     * @return Response
     * */
    public function lgu_data(Request $request)
    {
        $option = new CallCenterController;

        $lgu = DB::table('lgus')
            ->leftJoin("contact_people",'lgus.id','=','contact_people.lgu_id')
            ->leftJoin('users','contact_people.user_id','=','users.id')
            ->select('users.id as user_id','users.firstname','users.lastname','users.username','lgus.*','contact_people.id as contact_id','contact_people.contactno')
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
            "contactId"     => $lgu->user_id,
            "firstname"      => $lgu->firstname,
            "lastname"      => $lgu->lastname,
            "username"      => $lgu->username,
            "contactNo"     => $lgu->contactno,
            'postalCOde'    => $lgu->postalCode,
            'province_value'  => $option->getProvinces($lgu->region),
            'city_value'        => $option->getCities($lgu->province),
            'contactPeopleId'   => $lgu->contact_id,        ];

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
            'edit_contactperson_fname'   => 'required',
            'edit_contactperson_lname'   => 'required',
            'edit_contactperson_no'      => 'required',
            'ccId'                       => 'required',
        ]);

        if($validator->passes())
        {

            /*check if the submitted input matches the old data*/
            $checkinput = DB::table('lgus')
                ->leftJoin("contact_people",'lgus.id','=','contact_people.lgu_id')
                ->leftJoin('users','contact_people.user_id','=','users.id')
                ->select('lgus.*','contact_people.id as contact_id','contact_people.contactno')
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
                    ['users.firstname','=',$request->edit_contactperson_fname],
                    ['users.lastname','=',$request->edit_contactperson_lname],
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

                /*get the lgu previous data*/
                $lguDetails = DB::table('lgus')
                    ->leftJoin("contact_people",'lgus.id','=','contact_people.lgu_id')
                    ->leftJoin('users','contact_people.user_id','=','users.id')
                    ->select('lgus.*','contact_people.id as contact_id','contact_people.contactno','users.firstname','users.lastname')
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
                $contactPersonFname = $lguDetails->firstname;
                $contactPersonlname = $lguDetails->lastname;
                $contactPersonNo = $lguDetails->contactno;
                $contactId = $lguDetails->contact_id;
                $callCenterId = $lguDetails->call_center_id;

                if($lgu->save())
                {
                    $contatPerson = User::find($request->contactId);
                    $contatPerson->firstname = $request->edit_contactperson_fname;
                    $contatPerson->lastname = $request->edit_contactperson_lname;

                    if($contatPerson->save())
                    {
                        /*this will update the contact person details*/
                        $contactPersonTbl = ContactPerson::find($request->contactPeopleId);
                        $contactPersonTbl->contactno = $request->edit_contactperson_no;

                        /*update details of contact person*/
                        $contactPersonTbl->save();
                    }



                    /*display new data*/
                    $description = 'Updated LGU details';

                    $action = $this->device->userAgent();
                    $action .='<table class="table table-bordered">';
                    $action .= '<tr><td colspan="3"><b>Action: </b>'.$description.'</td></tr>';
                    $action .= '<tr><td></td><td><b>Previous</b></td><td><b>Updated</b></td></tr>';
                    $action .= '<tr><td colspan="3"><b>Call Center ID: </b>'.$request->ccId.'</td></tr>';
                    $action .= '<tr><td><b>Station Name</b></td><td>'.$stationname.'</td><td>'.$request->edit_station_name.'</td></tr>';
                    $action .= '<tr><td><b>Department</b></td><td>'.$department.'</td><td>'.$request->edit_department.'</td></tr>';
                    $action .= '<tr><td><b>Address</b></td><td>'.$address.'</td><td>'.$request->edit_street_address.'</td></tr>';
                    $action .= '<tr><td><b>City</b></td><td>'.$this->address->get_city_name($city).'</td><td>'.$this->address->get_city_name($request->edit_city).'</td></tr>';
                    $action .= '<tr><td><b>State</b></td><td>'.$this->address->get_province_name($state).'</td><td>'.$this->address->get_province_name($request->edit_state).'</td></tr>';
                    $action .= '<tr><td><b>Region</b></td><td>'.$this->address->getRegion($region).'</td><td>'.$this->address->getRegion($request->edit_region).'</td></tr>';
                    $action .= '<tr><td><b>Postal Code</b></td><td>'.$postalCode.'</td><td>'.$request->edit_postal_code.'</td></tr>';
                    $action .= '<tr><td><b>Contact Person</b></td><td>'.$contactPersonFname.' '.$contactPersonlname.'</td><td>'.$request->edit_contactperson_fname.' '.$request->edit_contactperson_lname.'</td></tr>';
                    $action .= '<tr><td><b>Contact Person Number</b></td><td>'.$contactPersonNo.'</td><td>'.$request->edit_contactperson_no.'</td></tr>';
                    $action .= '<tr><td><b>Call Center</b></td><td>'.CallCenter::find($callCenterId)->name.'</td><td>'.CallCenter::find($request->ccId)->name.'</td></tr>';
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
            $action .= '<tr><td colspan="2"><b>Action:</b> '.$description.'</td></tr>';
            $action .= '<tr><td><b>LGU ID</b></td><td>'.$request->lgu_delete_id.'</td></tr>';
            $action .= '<tr><td><b>Station Name</b></td><td>'.$lgu->station_name.'</td></tr>';
            $action .= '</table>';

            $this->activity->activity_log($action, $description);
            $message = ["success" => true];
        }else{
            $message = ["success" => false];
        }
        return response()->json($message);
    }
}
