@extends('layouts.auth.template')

@section('title', 'Inscription')

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-2">Créez votre espace Teamo</h1>
        <p class="text-secondary small mb-0">Rejoignez votre équipe et commencez à organiser vos projets.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" data-loading data-password-form>
        @csrf

        <div class="row g-3">
            <x-form.input field="name" label="Nom complet" :value="old('name')" autocomplete="name" autofocus
                placeholder="Votre nom complet" />
            <x-form.input field="email" label="Adresse e-mail" type="email" :value="old('email')"
                autocomplete="username" placeholder="vous@exemple.com" />
            <x-form.password field="password" label="Mot de passe" autocomplete="new-password"
                help="Au moins 8 caractères, une majuscule, un chiffre et un symbole."
                data-password-strength-input />

            <div class="teamo-password-strength mt-2" data-password-strength data-level="0" aria-live="polite">
                <div class="d-flex gap-1" aria-hidden="true">
                    <span data-strength-segment></span>
                    <span data-strength-segment></span>
                    <span data-strength-segment></span>
                    <span data-strength-segment></span>
                </div>
                <div class="d-flex flex-wrap column-gap-3 row-gap-1 mt-2 small text-secondary">
                    <span data-strength-rule="length">8 caractères minimum</span>
                    <span data-strength-rule="uppercase">Une majuscule</span>
                    <span data-strength-rule="digit">Un chiffre</span>
                    <span data-strength-rule="symbol">Un symbole</span>
                </div>
                <span class="visually-hidden" data-strength-label>Force du mot de passe</span>
            </div>

            <x-form.password field="password_confirmation" label="Confirmer le mot de passe"
                autocomplete="new-password" help="La confirmation doit correspondre au mot de passe."
                data-password-confirmation-input data-password-confirmation-target="password"
                data-password-confirmation-feedback="password_confirmation-help" />
        </div>

        <x-form.submit label="Créer mon compte" class="w-100 mt-4" />

        <p class="text-center text-secondary small mt-4 mb-0">
            Déjà un compte ?
            <a class="text-decoration-none" href="{{ route('login') }}">Se connecter</a>
        </p>
    </form>
@endsection
