<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Carbon;

class EmployeePageController extends Controller
{
    public function dashboard()
    {
        return view('Employee.employeeDashboard');
    }
}
