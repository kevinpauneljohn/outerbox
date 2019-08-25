<?php

namespace App\Http\Controllers\address;

use App\address\Municipality;
use App\address\Province;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function getProvinces(Request $request)
    {
        $provinces = Province::where('regCode',$request->id)->get();

        $value = "";
        foreach ($provinces as $province){
            $value .= '<option value="'.$province->provCode.'">'.$province->provDesc.'</option>';
        }

        return $value;
    }

    public function getCities(Request $request)
    {
        $cities = Municipality::where('provCode',$request->id)->get();

        $value = "";
        foreach ($cities as $city){
            $value .= '<option value="'.$city->citymunCode.'">'.$city->citymunDesc.'</option>';
        }

        return $value;
    }


    public static function regionName($regCode)
    {
        $region = DB::table('refregion')->where('regCode',$regCode)->first();
        return $region;
    }

    public static function provinceName($regProv)
    {
        $province = DB::table('refprovince')->where('provCode','=',$regProv)->first();
        return $province->provDesc;
    }

    public static function cityName($citymunCode)
    {
        $city = DB::table('refcitymun')->where('citymunCode',$citymunCode)->first();
        return $city->citymunDesc;
    }


}
