<?php

namespace App\Http\Controllers\Lgu;

use App\ContactPerson;
use App\Lgu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\User;

class LguController extends Controller
{
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
                $message = ['success' => true];
            }else{
                $message = ['success' => false];
            }

            return response()->json($message);
        }

        return response()->json($validator->errors());
    }

    public function assignContactPerson($lgu_id, $full_name, $contactNo)
    {
        $contactPerson = new ContactPerson;
        $contactPerson->lgu_id = $lgu_id;
        $contactPerson->fullname = $full_name;
        $contactPerson->contactno = $contactNo;
        $contactPerson->save();
    }

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
}
