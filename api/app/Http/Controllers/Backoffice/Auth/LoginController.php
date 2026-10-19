<?php

namespace App\Http\Controllers\Backoffice\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('backoffice.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'Identifiants incorrects.']);
        }

        $user = $request->user();

        if ($user->isSuspended() || ! $user->can('backoffice.access')) {
            Auth::logout();

            throw ValidationException::withMessages(['email' => 'Ce compte n\'a pas accès à l\'espace équipe.']);
        }

        $request->session()->regenerate();
        activity('auth')->causedBy($user)->log('Connexion au back-office');

        return redirect()->intended(route('backoffice.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('backoffice.login');
    }
}
