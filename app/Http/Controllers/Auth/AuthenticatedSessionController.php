<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();



        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if(session()->get('admin_user_id', false) !== false){
            return $this->backToAdminUser($request);
        }else{
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            Inertia::clearHistory();
            return redirect('/');
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function backToAdminUser(Request $request): RedirectResponse
    {
        $adminUserId = $request->session()->get('admin_user_id', false);
        $old_locale = session('locale');
        if($adminUserId !== false) {
            $newUser = User::find($adminUserId);
            session()->flush();
            session()->put('locale', $old_locale);
            Auth::login($newUser);
            Inertia::clearHistory();

            $flash_notification = [
                "title" => __('flash_bugtrackly.user_back_admin_title'),
                "text"  => __('flash_bugtrackly.user_back_admin_desc', ['user' => $newUser->full_name]),
            ];

            return to_route('settings.users.index')->with('success', $flash_notification);;
        }



        return redirect()->back();
    }
}
