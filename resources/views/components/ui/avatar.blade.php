@props(['user', 'size' => 'sm'])

@php
    $initials = mb_strtoupper(mb_substr($user->name, 0, 1));
    $avatarUrl = $user->avatar_url;
@endphp

@if ($avatarUrl)
    <img src="{{ $avatarUrl }}" alt="Photo de profil de {{ $user->name }}"
        {{ $attributes->merge(['class' => 'teamo-avatar teamo-avatar-' . $size . ' object-fit-cover']) }}>
@else
    <span {{ $attributes->merge(['class' => 'teamo-avatar teamo-avatar-' . $size . ' teamo-avatar-initials']) }}
        aria-hidden="true">{{ $initials }}</span>
@endif
