<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
//        Role::create(['name'=>'admin']);
//        Permission::create(['name' => 'add role']);

//        auth()->user()->givePermissionTo('add role');
//        auth()->user()->assignRole('admin');
//        $role = auth()->user()->getRoleNames()[0];
//
//        echo $role;
        return view('home');
    }
}
