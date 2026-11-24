@extends('layouts.auth.template')

@section('title', 'Mot de passe oublié')

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-2">Mot de passe oublié ?</h1>
        <p class="text-secondary small mb-0">
            Indiquez votre adresse e-mail et nous vous enverrons un lien de réinitialisation.
        </p>
    </div>

    @if (session('status'))
        <x-alert type="success" class="small mb-4">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}" data-loading>
        @csrf

        <x-form.input field="email" label="Adresse e-mail" type="email" :value="old('email')"
            autocomplete="username" autofocus placeholder="vous@exemple.com" />

        <x-form.submit label="Envoyer le lien" class="w-100 mt-4" />
    </form>

    @if (Route::has('login'))
        <p class="text-center text-secondary small mt-4 mb-0">
            <a class="text-decoration-none" href="{{ route('login') }}">Retour à la connexion</a>
        </p>
    @endif
@endsection
