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
use Spatie\Permission\Models\Role;

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

        $role = Role::where([
            ['name','=','Lgu'],
            ['deleted_at','=',null]
        ]);

        return CallCenter::find(12);
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
