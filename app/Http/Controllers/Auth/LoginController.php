<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\Models\UserLogs;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(){
        if(auth()->user()->status == 'INACTIVE'){
            Auth::logout();
            return redirect('/login?user=inactive');
        }
        
        $userlogs = new UserLogs;
        $userlogs->user_id = auth()->user()->id;
        $userlogs->activity = 'LOG-IN: User successfully logged in!';
        $userlogs->save();
    }

    protected function logout(){
        if(!Auth::guest()){
            $userlogs = new UserLogs;
            $userlogs->user_id = auth()->user()->id;
            $userlogs->activity = 'LOG-OUT: User successfully logged out!';
            $userlogs->save();
    
            Auth::logout();
        }
        
        return redirect('/login');
    }
}
