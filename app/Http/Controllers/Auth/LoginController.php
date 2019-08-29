<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
//    protected $redirectTo = '/home';
    protected $username;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->username = $this->findUsername();
    }

    public function findUsername()
    {
        $login = request()->input('login');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        request()->merge([$fieldType => $login]);

        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    public function redirectTo()
    {
        // User role
        $role = auth()->user()->getRoleNames()[0];
        //set active status to true before redirect
        $this->setActiveStatus(auth()->user()->id, 1);

            // Check user role
            switch ($role) {
                case 'super admin':
                    return '/super-admin/dashboard';
                    break;
                case 'admin':
                    return '/dashboard';
                    break;
                case 'agent':
                    return '/agent/dashboard';
                    break;
                default:
                    return '/login';
                    break;
            }
    }

    private function setActiveStatus($userId, $status)
    {
        DB::table('users')->where('id',$userId)->update(['active' => $status]);
    }

    public function logout(Request $request)
    {
        $current_id = auth()->user()->id;
        $this->guard()->logout();
        $request->session()->invalidate();

        $this->setActiveStatus($current_id, 0);
        return $this->loggedOut($request)?:redirect('/');
    }
}
