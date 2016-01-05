<?php
namespace App\Http\Controllers\Auth\Socials;

use App\Http\Controllers\Controller;
use App\Services\UsersService;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Response;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Illuminate\Session\SessionManager;

/**
 * @author Sergei Melnikov <me@rnr.name>
 */
class FacebookController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('guest', ['except' => 'logout']);
    }

    public function redirectToProvider(Socialite $socialite, SessionManager $manager)
    {
        $response = $socialite->driver('facebook')->redirect();

        $manager->driver()->save();

        return $response;
    }

    public function handleProviderCallback(Guard $auth, Socialite $socialite,
                                           UsersService $userService, SessionManager $manager)
    {
        $user = $socialite->driver('facebook')->user();

        $authUser = $userService->findOrCreateByFacebook($user);

        $auth->login($authUser);

        $manager->driver()->save();

        return redirect('/');
    }

}