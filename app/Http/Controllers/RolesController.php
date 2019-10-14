<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    /**
     * display role names with color coded labels
     * @param int $userId
     * @return mixed
     * */
    public function get_role_names_with_label($userId)
    {
        /**
         * @var $user
         * */
        /*$user = User::find($userId)->getRoleNames()[0];*/
        $user = ($userId != 0 )? User::find($userId)->getRoleNames()[0] : "system";

        switch ($user)
        {
            case "super admin":
                return '<small class="label bg-blue">super admin</small>';
                break;
            case "admin":
                return '<small class="label bg-yellow">admin</small>';
                break;
            case "agent":
                return '<small class="label bg-fuchsia">agent</small>';
                break;
            case "system":
                return '<small class="label bg-black">system</small>';
            default:
                return "";
                break;
        }
    }
}
