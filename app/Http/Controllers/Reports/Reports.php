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
        $lgu = DB::table('lgus')
            ->leftJoin("contact_people",'lgus.id','=','contact_people.lgu_id')
            ->select('lgus.*','contact_people.id as contact_id','contact_people.fullname','contact_people.contactno')
            ->where('lgus.id','=',1);

        return $lgu->first()->fullname;
    }

    /**
     * date created 09/30/2019
     * author: John Kevin Paunel
     * save user activity to database
     * @param string $action
     * @return void
     */
    public function activity_log($action)
    {
        $activity = new Activity;

        $activity->user_id = auth()->user()->id;
        $activity->action = auth()->user()->username.' '.$action;

        $activity->save();
    }
}
