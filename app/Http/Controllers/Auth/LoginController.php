<?php

namespace App\Http\Controllers\Auth;

use App\activity;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reports\Reports;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
     * set activity logs
     *
     * @var string
     * */
    private $activity;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->username = $this->findUsername();

        $this->activity = new Reports;
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


    /**
     * redirect login user to their designated dashboard
     * @return mixed
     * */
    public function redirectTo()
    {
            // Check user role
            $role = !empty(auth()->user()->getRoleNames()[0]) ? auth()->user()->getRoleNames()[0] : "";

            $this->setActiveStatus(auth()->user()->id, 1);
            Cache::put('user-is-online-', auth()->user()->id, 500);
            $this->activity->activity_log(" logged in","User logged in");

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

    /**
     * set active status of login users
     * @param int $userId
     * @param int $status
     * @return void
     * */
    private function setActiveStatus($userId, $status)
    {
        DB::table('users')->where('id',$userId)->update(['active' => $status]);
    }

    public function logout(Request $request)
    {
        $current_id = auth()->user()->id;

        $this->activity->activity_log(" logged out","User Logged Out");

        $this->guard()->logout();
        $request->session()->invalidate();

        $this->setActiveStatus($current_id, 0);

        return $this->loggedOut($request)?:redirect('/');
    }
}
