<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * USE EMAIL for login (IMPORTANT)
     */
    public function username()
    {
        return 'username';
    }

    /**
     * After login logic
     */
    public function authenticated(Request $request, $user)
    {
        
        // ✅ Role-based redirect
        switch ($user->role_id) {

            case 3:
            case 4:
                return redirect()
                    ->route('home')
                    ->withSuccess('Successfully Login');

            case 6:
                return redirect()
                    ->route('home')
                    ->withSuccess('Successfully Login');

            case 5:
            case 7:
                Auth::logout();
                return redirect()->route('login')
                    ->withErrors('You have no access to this system. Please contact your leader!');

            default:
                return redirect()
                    ->route('home')
                    ->withSuccess('Successfully Login');
        }
    }
}