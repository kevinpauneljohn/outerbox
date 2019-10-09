<?php

namespace App\Http\Controllers\Reports;

use App\activity;
use App\Http\Controllers\address\AddressController;
use App\Models\CallCenter;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use JasperPHP\JasperPHP;

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
        return (auth()->user() == null) ? "null" : "not null";
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
        $activity->action = auth()->user()->username.' '.$action;
        $activity->description = $description;

        $activity->save();
    }

    public function system_activity_log($action)
    {
        $activity = new Activity;

        $activity->user_id = 0;
        $activity->action = 'The system '.$action;

        $activity->save();
    }
}
