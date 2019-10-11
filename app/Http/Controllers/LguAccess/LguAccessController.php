<?php

namespace App\Http\Controllers\LguAccess;

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
}
