<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\Auth;

use Socialite;
use App\User;

class LoginController extends Controller
{

    use AuthenticatesUsers;
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider($website)
    {
        return Socialite::driver($website)->redirect();
    }

    public function handleProviderCallback($website)
    {   

        if($website == 'github') {
            $user = Socialite::driver($website)->user();
        }else {
            $user = Socialite::driver($website)->stateless()->user();
        }
        // login if the user is in the database

        $user_found = User::where('email', $user->getEmail())->first();

        if($user_found) {
            Auth::login($user_found);
            return redirect('/');
        }else {
            // or we need to make a new user 

            $new_user = new User;

            $new_user->name = $user->getName();
            $new_user->email = $user->getEmail();

            $new_user->password = bcrypt(123456);

            if($new_user->save()) {
                Auth::login($new_user);
                return redirect('/');
            }
        }
    }
}
