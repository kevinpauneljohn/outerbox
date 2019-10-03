<?php

namespace App\Http\Controllers\Reports;

use App\activity;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

        return auth()->user()->username;
    }

    /**
     * date created 09/30/2019
     * author: John Kevin Paunel
     * save user activity to database
     * @param int $action
     * @return void
     */
    public function activity_log($action)
    {
        $activity = new Activity;

        $activity->user_id = auth()->user()->id;
        $activity->action = $action;

        $activity->save();
    }
}
