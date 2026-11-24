@extends('layouts.auth.template')

@section('title', 'Confirmation du mot de passe')

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-2">Zone sécurisée</h1>
        <p class="text-secondary small mb-0">Confirmez votre mot de passe pour continuer.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" data-loading>
        @csrf

        <x-form.password field="password" label="Mot de passe" autocomplete="current-password" autofocus />

        <x-form.submit label="Confirmer" class="w-100 mt-4" />
    </form>
@endsection
