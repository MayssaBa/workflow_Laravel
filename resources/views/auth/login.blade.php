@extends('layouts.auth.template')

@section('title', 'Connexion')

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-2">Bon retour sur Teamo</h1>
        <p class="text-secondary small mb-0">Connectez-vous pour retrouver vos projets, vos tâches et vos équipes.</p>
    </div>

    @if (session('status'))
        <x-alert type="success" class="small mb-4">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('login') }}" data-loading>
        @csrf

        <div class="row g-3">
            <x-form.input field="email" label="Adresse e-mail" type="email" :value="old('email')"
                autocomplete="username" autofocus placeholder="vous@exemple.com" />
            <x-form.password field="password" label="Mot de passe" autocomplete="current-password"
                placeholder="Votre mot de passe" />
        </div>

        <div class="d-flex align-items-center justify-content-between gap-3 my-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                <label class="form-check-label small" for="remember_me">Se souvenir de moi</label>
            </div>

            @if (Route::has('password.request'))
                <a class="small text-decoration-none" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
            @endif
        </div>

        <x-form.submit label="Se connecter" class="w-100" />

        @if (Route::has('register'))
            <p class="text-center text-secondary small mt-4 mb-0">
                Vous n’avez pas encore de compte ?
                <a class="text-decoration-none" href="{{ route('register') }}">Créer un compte</a>
            </p>
        @endif
    </form>
@endsection
