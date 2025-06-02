<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthWebController extends Controller
{
    /**
     * Pokaż formularz logowania
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Przetwórz logowanie
     */
    public function processLogin(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            return redirect()->intended('/dashboard')
                ->with('success', 'Zalogowano pomyślnie!');
        }

        throw ValidationException::withMessages([
            'email' => 'Nieprawidłowe dane logowania.',
        ]);
    }

    /**
     * Pokaż formularz rejestracji
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Przetwórz rejestrację
     */
    public function processRegister(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted',
        ], [
            'name.required' => 'Imię i nazwisko jest wymagane.',
            'email.required' => 'Adres email jest wymagany.',
            'email.email' => 'Podaj prawidłowy adres email.',
            'email.unique' => 'Konto z tym adresem email już istnieje.',
            'password.required' => 'Hasło jest wymagane.',
            'password.min' => 'Hasło musi mieć minimum 8 znaków.',
            'password.confirmed' => 'Hasła nie są identyczne.',
            'terms.required' => 'Musisz zaakceptować regulamin.',
            'terms.accepted' => 'Musisz zaakceptować regulamin.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect('/dashboard')
            ->with('success', 'Konto zostało utworzone pomyślnie!');
    }

    /**
     * Wyloguj użytkownika
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', 'Wylogowano pomyślnie!');
    }
}