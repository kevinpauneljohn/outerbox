<?php

namespace App\Http\Controllers\Lgu;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LguController extends Controller
{
    public function addLgu(Request $request)
    {
        return $request->all();
    }
}
