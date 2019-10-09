<?php

namespace App\Http\Controllers;

use App\activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * fetch the action done
     * @param Request $request
     * @return mixed
     * */
    public function display_description(Request $request)
    {
        $activity = activity::find($request->id);
        return $activity->action;
    }
}
