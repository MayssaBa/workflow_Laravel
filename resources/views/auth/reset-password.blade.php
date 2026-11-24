@extends('layouts.auth.template')

@section('title', 'Réinitialisation du mot de passe')

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-2">Nouveau mot de passe</h1>
        <p class="text-secondary small mb-0">Choisissez un mot de passe pour sécuriser votre compte Teamo.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" data-loading>
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="row g-3">
            <x-form.input field="email" label="Adresse e-mail" type="email" :value="old('email', $request->email)"
                autocomplete="username" autofocus readonly />
            <x-form.password field="password" label="Mot de passe" autocomplete="new-password" />
            <x-form.password field="password_confirmation" label="Confirmer le mot de passe"
                autocomplete="new-password" />
        </div>

        <x-form.submit label="Réinitialiser le mot de passe" class="w-100 mt-4" />
    </form>

    @if (Route::has('login'))
        <p class="text-center text-secondary small mt-4 mb-0">
            <a class="text-decoration-none" href="{{ route('login') }}">Retour à la connexion</a>
        </p>
    @endif
@endsection
