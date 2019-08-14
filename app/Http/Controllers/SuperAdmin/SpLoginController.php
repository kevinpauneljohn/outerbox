<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SpLoginController extends Controller
{
    public function login_form()
    {
        return view('SuperAdmin.login');
    }
}
