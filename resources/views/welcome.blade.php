@extends('layouts.auth.template')

@section('title', 'Accueil')

@section('content')
    <div class="text-center">
        <h1 class="h3 fw-bold mb-2">Bienvenue sur Teamo</h1>
        <p class="text-secondary mb-4">Organisez vos projets, vos tâches et vos équipes au même endroit.</p>

        @if (Route::has('login'))
            <div class="d-grid gap-2">
                @auth
                    <a class="btn btn-primary btn-lg" href="{{ url('/dashboard') }}">Accéder au tableau de bord</a>
                @else
                    <a class="btn btn-primary btn-lg" href="{{ route('login') }}">Se connecter</a>

                    @if (Route::has('register'))
                        <a class="btn btn-outline-secondary btn-lg" href="{{ route('register') }}">Créer un compte</a>
                    @endif
                @endauth
            </div>
        @endif
    </div>
@endsection
