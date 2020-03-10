<?php

namespace App\Http\Controllers\Auth;

use App\Events\MyEvent;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Routing\Redirector;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return RedirectResponse
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\RedirectResponse|Redirector
     */
    public function handleProviderCallback()
    {

        try {

            $user = Socialite::driver('google')->user();

            // check if they're an existing user
            $existingUser = User::where('provider', 'google')
                ->where('provider_id', $user->id)
                ->first();

            event(new MyEvent('hello world'));

            if ($existingUser) {

                /** @var User $existingUser */

                if ($existingUser->approved) {

                    // log them in
                    auth()->login($existingUser, true);

                    return redirect()->route('play');

                } else {

                    return redirect()->route('homepage')->withErrors("Attendi che la mail venga abilitata!");

                }


            } else {
                // create a new user
                $newUser = new User;
                $newUser->name = $user->name;
                $newUser->email = $user->email;

                $newUser->provider = 'google';
                $newUser->provider_id = $user->id;

                $newUser->avatar = $user->avatar;
                $newUser->avatar_original = $user->avatar_original;

                $newUser->save();

            }

        } catch (Exception $e) {

            return redirect()->route('homepage')->withErrors("CAZZO! qualcosa non va");

        }

        return redirect()->route('homepage');

    }


}
