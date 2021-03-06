<?php

namespace App\Http\Controllers\Web;

use Auth;
use Socialite;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;


class AuthenticationController extends Controller
{
    /**
     * Initializes the social login defined by the user. Can be
     * Facebook, Google +, or Twitter.
     *
     * @param string $account The account the user is logging in with.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getSocialRedirect($account)
    {
        try {
            return Socialite::driver($account)->redirect();   //Laravel 5.7

        } catch (\InvalidArgumentException $e) {
            return redirect('/login');
        }
    }

    /**
     * Handles the call back from the social network.
     *
     * @param string $account The social account that is handling the log in.
     * @return \Illuminate\Routing\Redirector
     */
    public function getSocialCallback($account)
    {
        /*
           Grabs the user who authenticated via social account.
        */
//        $socialUser = Socialite::with( $account )->user();
        $socialUser = Socialite::driver($account)->user();   //Laravel 5.7


        /*
          Gets the user in our database where the provider ID
          returned matches a user we have stored.
         */
        $user = User::where('provider_id', '=', $socialUser->id)
            ->where('provider', '=', $account)
            ->first();

        /*
          Checks to see if a user exists. If not we need to create the
          user in the database before logging them in.
        */
        if ($user == null) {
            $newUser = new User();

            $newUser->name = $socialUser->getName();
            $newUser->email = $socialUser->getEmail() == '' ? '' : $socialUser->getEmail();
            $newUser->avatar = $socialUser->getAvatar();
            $newUser->password = '';
            $newUser->provider = $account;
            $newUser->provider_id = $socialUser->getId();
            $newUser->favorite_coffee = '';
            $newUser->flavor_notes = '';
            $newUser->city = '';
            $newUser->state = '';

            $newUser->save();

            $user = $newUser;
        }

        /*
          Log in the user to generate laravel_token
        */
        Auth::login($user);

        /*
          Redirect to the app
        */
        return redirect('/');
    }
}