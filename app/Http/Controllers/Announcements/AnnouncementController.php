<?php

namespace App\Http\Controllers\Announcements;

use App\Announcement;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{

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
            $announce->status = "draft";

            if($announce->save())
            {
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
            case 'draft':
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
            /*this will check if the submitted update is not already approved by the super admin so they can still edit it*/
            $check = Announcement::where([
                ['id','=',$request->announcementId],
                ['status','=','draft'],
            ])->count();

            if($check > 0)
            {
                $announcement = Announcement::find($request->announcementId);
                $announcement->title = $request->edit_title;
                $announcement->description = $request->edit_description;

                    $message = $announcement->save() ? ['success' => true] : ['success' => false];
            }else{
             $message = ['error' => 'Action is not allowed!', 'success' => false];
            }
            return response()->json($message);
        }

        return response()->json($validator->errors());
    }
}
