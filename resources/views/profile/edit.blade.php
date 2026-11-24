@extends('layouts.admin.template')

@section('title', 'Mon profil')

@section('content')
    @php
        $activeTab = 'informations';

        if ($errors->userDeletion->isNotEmpty() || $errors->hasAny(['current_password', 'password', 'password_confirmation'])) {
            $activeTab = 'securite';
        } elseif ($errors->hasAny(['name', 'email', 'phone', 'avatar'])) {
            $activeTab = 'informations';
        }

        $profileTabs = [
            'informations' => 'Informations personnelles',
            'securite' => 'Sécurité',
            'compte' => 'Compte',
            'preferences' => 'Préférences',
        ];
    @endphp

    @if ($errors->userDeletion->isNotEmpty())
        <x-alert type="danger">Le formulaire de suppression du compte contient des erreurs.</x-alert>
    @endif

    <section class="card shadow-sm mb-4">
        <div class="card-body d-flex flex-column flex-lg-row align-items-lg-center gap-4">
            <x-ui.avatar :user="$user" size="lg" />

            <div class="min-w-0">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <h1 class="h3 fw-bold mb-0">{{ $user->name }}</h1>
                    <x-status-badge :status="$user->role" />
                </div>
                <p class="text-secondary mb-2">{{ $user->email }}</p>
                <x-status-badge :status="$user->is_active ? 'actif' : 'inactif'" />
            </div>

            <div class="ms-lg-auto">
                <button type="button" class="btn btn-primary" data-teamo-profile-edit>Modifier le profil</button>
            </div>
        </div>
    </section>

    <section class="card shadow-sm">
        <div class="card-body">
            <ul class="nav nav-pills teamo-profile-tabs mb-4" role="tablist" aria-label="Sections du profil"
                data-teamo-profile-tabs data-teamo-active-tab="{{ $activeTab }}">
                @foreach ($profileTabs as $key => $label)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === $key ? 'active' : '' }}" id="{{ $key }}-tab"
                            data-bs-toggle="pill" data-bs-target="#{{ $key }}" type="button" role="tab"
                            aria-controls="{{ $key }}" aria-selected="{{ $activeTab === $key ? 'true' : 'false' }}"
                            tabindex="{{ $activeTab === $key ? '0' : '-1' }}">{{ $label }}</button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade {{ $activeTab === 'informations' ? 'show active' : '' }}" id="informations"
                    role="tabpanel" aria-labelledby="informations-tab" tabindex="0">
                    @include('profile.partials.update-profile-information-form')
                </div>

                <div class="tab-pane fade {{ $activeTab === 'securite' ? 'show active' : '' }}" id="securite"
                    role="tabpanel" aria-labelledby="securite-tab" tabindex="0">
                    @include('profile.partials.update-password-form')
                    <hr class="my-4">
                    @include('profile.partials.delete-user-form')
                </div>

                <div class="tab-pane fade {{ $activeTab === 'compte' ? 'show active' : '' }}" id="compte"
                    role="tabpanel" aria-labelledby="compte-tab" tabindex="0">
                    @include('profile.partials.account-information')
                </div>

                <div class="tab-pane fade {{ $activeTab === 'preferences' ? 'show active' : '' }}" id="preferences"
                    role="tabpanel" aria-labelledby="preferences-tab" tabindex="0">
                    @include('profile.partials.preferences')
                </div>
            </div>
        </div>
    </section>
@endsection
