<?php

namespace App\Http\Controllers\Reports;

use App\activity;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PdfReport;

class Reports extends Controller
{
    /**
     * test JasperPHP report
     * @return void
     * */
    public function generateReport()
    {
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
        return PdfReport::of($title, $meta, $queryBuilder, $columns)
            ->setOrientation('landscape')
            ->download('activity_log_'.Carbon::now());
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
