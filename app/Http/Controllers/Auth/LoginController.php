<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\APIController;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User\User;
use App\Models\User\Account;

class LoginController extends APIController
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
    protected $redirectTo = '/home';


	public function redirectToProvider($provider)
	{
		if($this->api) return $this->reply(Socialite::driver($provider)->stateless()->redirect()->getTargetUrl());
		return Socialite::driver($provider)->redirect();
	}

	public function handleProviderCallback($provider)
	{
		$user = \Socialite::driver($provider)->user();
		$user = $this->createOrGetUser($user,$provider);
		\Auth::login($user);
		if($this->api) return $user;
		return redirect()->route('home');
	}

	public function createOrGetUser($providerUser,$provider)
	{
		$account = Account::where('provider',$provider)->where('provider_user_id',$providerUser->getId())->first();
		if (!$account) {
			$account = new Account(['provider_user_id' => $providerUser->getId(),'provider' => $provider]);
			$user = User::where('email',$providerUser->getEmail())->first();
			if (!$user) {
				$user = User::create([
					'id'       => str_random(24),
					'nickname' => $providerUser->getNickname(),
					'email'    => $providerUser->getEmail(),
					'name'     => $providerUser->getName(),
				]);
			}
			$account->user()->associate($user);
			$account->save();
			return $user;
		}
		return $account->user;
	}
}
