<?php

namespace App\Http\Controllers\Lgu;

use App\ContactPerson;
use App\Http\Controllers\address\AddressController;
use App\Http\Controllers\Reports\Reports;
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

    public function __construct()
    {
        $this->activity = new Reports;
        $this->address = new AddressController;
    }

    /**
     * add new LGU
     * @param Request $request
     * @return Response
     * */
    public function addLgu(Request $request)
    {
        $user = User::find(Auth::user()->id)->callcenter;
        $callcenter_id = $user[0]->pivot->cc_id;
        $validator = Validator::make($request->all(),[
            'station_name'          => 'required|max:50',
            'department'            => 'required|max:50',
            'street_address'        => 'required:max:100',
            'region'                => 'required',
            'state'                 => 'required',
            'city'                  => 'required|max:60',
            'contactperson_name'    => 'required',
            'contactperson_no'      => 'required'
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

            if($lgu->save())
            {
                if(!empty($request->contactperson_name) && !empty($request->contactperson_no))
                {
                    $this->assignContactPerson($lgu->id, $request->contactperson_name, $request->contactperson_no);
                }

                /*Activity log*/
                $action = "added a new LGU - Station Name: ".$request->station_name;
                $action .= ", Department: ".$request->department;
                $action .= ", Location: ".$request->street_address.", ".$this->address->get_city_name($request->city).", "
                    .$this->address->get_province_name($request->state).", ".$this->address->getRegion($request->region);
                $action .= ", with Contact Person: ".$request->contactperson_name.", Contact Person Number: ".$request->contactperson_no;

                $action .= "  assigned to Call Center: ".CallCenter::find($callcenter_id)->name;
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
            "stationName"   => $lgu->station_name,
            "department"    => $lgu->department,
            "region"        => $lgu->region,
            "province"      => $lgu->province,
            "city"          => $lgu->city,
            "address"       => $lgu->address,
            "contactId"     => $lgu->contact_id,
            "fullname"      => $lgu->fullname,
            "contactNo"     => $lgu->contactno
        ];

        return response()->json($data);
    }
}
