<?php

namespace App\Http\Controllers\Reports;

use App\activity;
use App\Http\Controllers\UserAgentController;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PdfReport;
use ExcelReport;

class Reports extends Controller
{

    /**
     * @var $device
     * */
    private $device;

    public function __construct()
    {
        $this->device = new UserAgentController;
    }

    /**
     * test JasperPHP report
     * @return void
     * */
    public function generateReport()
    {
        return auth()->user()->id;
    }

    /**
     * Oct. 16, 2019
     * @author john kevin paunel
     * this will generate pdf reports
     * @param Request $request
     * @return mixed
     * */
    public function generatePdfReport(Request $request)
    {
        $fromDate = $request->startDate;
        $toDate = $request->endDate;
        $sortBy = "created_at";

        $title = 'Registered User Report'; // Report title

        $meta = [ // For displaying filters description on header
            'Date Range' => $fromDate . ' To ' . $toDate,
            'Sort By' => $sortBy
        ];


        $queryBuilder = activity::select(['user_id','action','description','created_at'])
            ->where('user_id','=',$request->userId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy($sortBy);

        $columns = [ // Set Column to be displayed
            'Username' => function($user){
                return User::find($user->user_id)->username;
            },
            'Action' => function($user){
                return strip_tags($user->action);
            },
            'Description' => 'description',
            'Created At' => 'created_at'
        ];

        // Generate Report with flexibility to manipulate column class even manipulate column value (using Carbon, etc).
        if($request->action == "pdf")
        {
            return PdfReport::of($title, $meta, $queryBuilder, $columns)
                ->setOrientation('landscape')
                ->download('activity_log_'.Carbon::now());
        }elseif ($request->action == "excel"){
            return ExcelReport::of($title, $meta, $queryBuilder, $columns)
                ->simple()
                ->download('activity_log_'.Carbon::now());
        }
    }


    /**
     * Oct. 31, 2019
     * @author john kevin paunel
     * This will generate all activity reports
     * js file used: reports.js
     * @param Request $request
     * @return mixed
     * */
    public function generateAllActivity(Request $request)
    {
        $fromDate = $request->startDate;
        $toDate = $request->endDate;
        $sortBy = "created_at";

        $title = 'Registered User Report'; // Report title

        $meta = [ // For displaying filters description on header
            'Date Range' => $fromDate . ' To ' . $toDate,
            'Sort By' => $sortBy
        ];


        $queryBuilder = activity::select(['user_id','action','description','created_at'])
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy($sortBy);

        $columns = [ // Set Column to be displayed
            'Username' => function($user){
                return ($user->user_id === 0) ? "System" : User::find($user->user_id)->username;
            },
            'Action' => function($user){
                return strip_tags($user->action);
            },
            'Description' => 'description',
            'Created At' => 'created_at'
        ];

        // Generate Report with flexibility to manipulate column class even manipulate column value (using Carbon, etc).
        if($request->action == "pdf")
        {
            return PdfReport::of($title, $meta, $queryBuilder, $columns)
                ->setOrientation('landscape')
                ->download('activity_log_'.Carbon::now());
        }elseif ($request->action == "excel"){
            return ExcelReport::of($title, $meta, $queryBuilder, $columns)
                ->simple()
                ->download('activity_log_'.Carbon::now());
        }
    }

    /**
     * date created 09/30/2019
     * @author John Kevin Paunel
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
