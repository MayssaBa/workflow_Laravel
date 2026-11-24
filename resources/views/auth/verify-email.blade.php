@extends('layouts.auth.template')

@section('title', 'Vérification de l’e-mail')

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-2">Vérifiez votre adresse e-mail</h1>
        <p class="text-secondary small mb-0">
            Avant de commencer, confirmez votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer.
            Si vous ne l’avez pas reçu, nous pouvons vous en envoyer un autre.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <x-alert type="success" class="small mb-4">Un nouveau lien de vérification a été envoyé à votre adresse e-mail.</x-alert>
    @endif

    <div class="d-flex flex-column flex-sm-row align-items-stretch gap-3">
        <form method="POST" action="{{ route('verification.send') }}" data-loading>
            @csrf
            <x-form.submit label="Renvoyer le lien" variant="outline-secondary" class="w-100" />
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-secondary w-100">Se déconnecter</button>
        </form>
    </div>
@endsection
