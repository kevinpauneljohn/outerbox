<?php

namespace App\Http\Controllers\Announcements;

use App\Announcement;
use App\Http\Controllers\Reports\Reports;
use App\Http\Controllers\UserAgentController;
use App\Models\CallCenter;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{

    /**
     * @var $activity
     * */
    public $activity;
    /**
     * @var $device
     * */
    private $device;

    public function __construct()
    {
        $this->activity = new Reports();
        $this->device = new UserAgentController();
    }

    /**
     * Oct 18, 2019
     * @author john kevin paunel
     * Will Create announcement by lgus
     * @param Request $request
     * @return object
     * */
    public function addAnnouncement(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title'         => 'required|max:300',
            'description'   => 'required'
        ]);

        if($validator->passes())
        {

            $announce = new Announcement();
            $announce->user_id = auth()->user()->id;
            $announce->title = $request->title;
            $announce->description = $request->description;
            $announce->status = "pending";

            if($announce->save())
            {
                $action = $this->device->userAgent();
                $action .= '<table class="table table-bordered">';
                $action .= '<tr><td colspan="2"><b>Action:</b> Added New Announcement</td></tr>';
                $action .= '<tr>';
                $action .= '<td><b>Title</b></td><td>'.$request->title.'</td>';
                $action .= '<td><b>Description</b></td><td>'.$request->description.'</td>';
                $action .= '<td><b>Status</b></td><td>Draft</td>';
                $action .= '</tr>';
                $action .= '</table>';

                $description = "Added New Announcement";
                $this->activity->activity_log($action, $description);

                $message = ['success' => true];
            }else{
                $message = ['success' => false];
            }
            return response()->json($message);
        }
        return response()->json($validator->errors());
    }


    /**
     * Oct. 18, 2019
     * @author john kevin paunel
     * display colored label status for announcement column
     * @param int $status
     * @return mixed
     * */
    public function announcementStatus($status)
    {
        switch ($status){
            case 'pending':
                return '<small class="label bg-yellow">'.$status.'</small>';
                break;
            case 'approved':
                return '<small class="label label-success">'.$status.'</small>';
                break;
        }
    }

    /**
     * Oct. 18, 2019
     * @author john kevin paunel
     * Display announcement details in modal
     * @param Request $request
     * @return object
     * view: announcement.blade
     * */
    public function displayAnnouncementDetails(Request $request)
    {
        return Announcement::find($request->id);
    }

    /**
     * Oct. 25, 2019
     * @author john kevin paunel
     * this will update the announcement details
     * @param Request $request
     * @return mixed
     * */
    public function updateAnnouncementDetails(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'edit_title'         => 'required|max:300',
            'edit_description'   => 'required'
        ]);

        if($validator->passes())
        {
            /**
             * @var object $checkIfmatched
             * this will check if the submitted input matches the date on the database
             */
            $checkIfmatched = Announcement::where([
                ['id','=',$request->announcementId],
                ['title','=',$request->edit_title],
                ['description','=',$request->edit_description],
            ])->count();

            if($checkIfmatched == 0)
            {
                /**
                 * @var object $checkIfApproved
                 * this will check if the submitted update is not already approved by the super admin so they can still edit it
                 * */
                $checkIfApproved = Announcement::where([
                    ['id','=',$request->announcementId],
                    ['status','=','pending'],
                ])->count();

                if($checkIfApproved > 0)
                {
                    /**
                     * @var $announcement
                     * this will update the previous announcement by ID
                     * */
                    $announcement = Announcement::find($request->announcementId);
                    $announcement->title = $request->edit_title;
                    $announcement->description = $request->edit_description;

                    /**
                     * @var $prevAnnouncement
                     * this will retrieve the previous announcement
                     * */
                    $prevAnnouncement = Announcement::find($request->announcementId);

                    /**
                     * @var $action
                     * this will log activity
                     * */
                    $action = $this->device->userAgent();
                    $action .= '<table class="table table-bordered">';
                    $action .= '<thead><tr><td></td><td><b>Previous</b></td><td><b>Updated</td></tr></thead>';
                    $action .= '<tr><td><b>Title</b></td><td>'.$prevAnnouncement->title.'</td><td>'.$request->edit_title.'</td></tr>';
                    $action .= '<tr><td><b>Description</b></td><td>'.$prevAnnouncement->description.'</td><td>'.$request->edit_description.'</td></tr>';
                    $action .= '</table>';

                    $description = "Updated Announcement";
                    $this->activity->activity_log($action, $description);

                    $message = $announcement->save() ? ['success' => true] : ['success' => false];
                }else{
                    $message = ['error' => 'Action is not allowed!', 'success' => false];
                }
            }else{
                $message = ['error' => 'No changes occurred', 'success' => false];
            }

            return response()->json($message);
        }

        return response()->json($validator->errors());
    }

    /**
     * Oct. 25, 2019
     * @author john kevin paunel
     * announcement approval by the super admin
     * @param Request $request
     * @return mixed
     * */
    public function announcementApproval(Request $request)
    {
        $data = explode('-',$request->data);

       $status = $data[0];
       $id = $data[1];

       $announcement = Announcement::find($id);
       $announcement->status = ($status == 'pending') ? 'approved' : 'pending';

       /**
        * var $prevAnnouncement
        * retrieve the approved announcement detail
        * */
       $prevAnnouncement = Announcement::find($id);
       #activity logs
        $action = $this->device->userAgent();
        $action .= '<table class="table table-bordered">';
        $action .= '<tr><td><b>Action: </b></td><td> Approved Announcement</td>';
        $action .= '<tr><td><b>Title</b></td><td>'.$prevAnnouncement->title.'</td>';
        $action .= '<tr><td><b>Description</b></td><td>'.$prevAnnouncement->description.'</td>';
        $action .= '<tr><td><b>Created by</b></td><td>'.User::find($prevAnnouncement->user_id)->username.'</td>';
        $action .= '<tr><td><b>LGU</b></td><td>'.User::find($prevAnnouncement->user_id)->lgus->first()->station_name.'</td>';
        $action .= '<tr><td><b>Status</b></td><td>';
            if($prevAnnouncement->status == 'approved'){
                $action .= 'Unapproved';
            }else{
                $action .= 'approved';
            }
        $action .= '</td>';
        $action .= '</table>';

        $this->activity->activity_log($action, "Announcement status updated by super admin");

       $message = $announcement->save() ? ['success' => true] : ['success' => false];

       return response()->json($message);

    }

    /**
     * Announcement Fetching from mobile app
     * @param request
     * @author Jovito Pangan
     * Updated December 03, 2019
     */
    public function announcementList(Request $request)
    {

        $announcement = DB::table('announcements')
        ->select('announcements.user_id', 'announcements.title', 'announcements.description', 'announcements.status')
        ->where('status', 'approved')
        ->get();

        return json_decode($announcement);
    }
}
