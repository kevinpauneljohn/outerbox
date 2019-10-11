<?php

namespace App\Http\Controllers\Reports;

use App\activity;
use App\Http\Controllers\address\AddressController;
use App\Http\Controllers\TimeController;
use App\Models\CallCenter;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use JasperPHP\JasperPHP;
use Jenssegers\Agent\Agent;

class Reports extends Controller
{

    /**
     * test JasperPHP report
     * @return void
     * */
    public function generateReport()
    {
        //jasper ready to call
//        JasperPHP::compile(base_path('/vendor/cossou/jasperphp/examples/hello_world.jrxml'))->execute();
//        return auth()->user()->id;

        // server ip


        // server ip

       // echo \request()->ip();
        // server ip
        $user_id = 12;
        $user = ($user_id != 0 )? User::find($user_id)->getRoleNames()[0] : "system";
        return $user;
    }
    public function getUserIpAddr(){
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    /**
     * date created 09/30/2019
     * author: John Kevin Paunel
     * save user activity to database
     * @param string $action
     * @param string $description
     * @return void
     */
    public function activity_log($action,$description)
    {
        $activity = new Activity;

        if(auth()->user() == null)
        {

        }
        $activity->user_id = auth()->user()->id;
        $activity->action = $action;
        $activity->description = $description;

        $activity->save();
    }

    /**
     * system logs
     * @param string $action
     * @param string $description
     * @return void
     * */
    public function system_activity_log($action,$description)
    {
        /**
         * @var $activity
         * */
        $activity = new Activity;

        $activity->user_id = 0;
        $activity->action = $action;
        $activity->description = $description;

        $activity->save();
    }
}
