<?php

namespace App\Http\Controllers\LguAccess;

use App\Announcement;
use App\Http\Controllers\Announcements\AnnouncementController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LguAccessController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Lgu access age
     * @return mixed
     * */
    public function dashboard()
    {
        return view('LguAccess.dashboard');
    }

    /**
     * Lgu Announcement Page
     * */
    public function announcement()
    {
        return view('LguAccess.announcement')->with([
            'announcements'     => Announcement::all(),
            'status'            => new AnnouncementController(),
        ]);
    }
}
