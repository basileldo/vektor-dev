<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Vektor\Api\Http\Controllers\ApiController;

class LoginController extends ApiController
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
        parent::__construct();
        $this->middleware('guest')->except(['logout', 'isLoggedIn']);
    }

    /**
     * Show the application's login form.
     */
    public function showLoginForm(): View
    {
        return view('login');
    }

    /**
     * The user has been authenticated.
     *
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        return $this->response([
            'success' => true,
            'success_message' => 'You have logged in successfully',
            'data' => [
                'redirect_url' => url($this->redirectTo),
            ],
        ]);
    }

    /**
     * Get the failed login response instance.
     *
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request): Response
    {
        return $this->response([
            'error' => true,
            'error_message' => trans('auth.failed'),
        ]);
    }

    /**
     * Get the logged in state of the application.
     *
     * @return mixed
     */
    public function isLoggedIn()
    {
        $user = Auth::user();

        return $this->response([
            'success' => true,
            'success_message' => $user ? 'The user is logged in' : 'The user is not logged in',
            'data' => [
                'is_logged_in' => $user ? true : false,
            ],
        ]);
    }

    /**
     * Get the user existence state.
     *
     * @return mixed
     */
    public function exists(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();

        return $this->response([
            'success' => true,
            'success_message' => $user ? 'The user exists' : 'The user does not exist',
            'data' => [
                'exists' => $user ? true : false,
            ],
        ]);
    }
}
