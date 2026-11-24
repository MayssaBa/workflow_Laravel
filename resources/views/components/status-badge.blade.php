@props(['status'])

@php
    $key = $status instanceof \BackedEnum ? (string) $status->value : (string) $status;
    $key = strtolower(trim($key));

    $map = [
        'active' => ['Actif', 'success'],
        'actif' => ['Actif', 'success'],
        'inactive' => ['Désactivé', 'danger'],
        'inactif' => ['Désactivé', 'danger'],
        'todo' => ['À faire', 'secondary'],
        'to_do' => ['À faire', 'secondary'],
        'a_faire' => ['À faire', 'secondary'],
        'in_progress' => ['En cours', 'primary'],
        'en_cours' => ['En cours', 'primary'],
        'blocked' => ['Bloquée', 'danger'],
        'bloquee' => ['Bloquée', 'danger'],
        'done' => ['Terminée', 'success'],
        'terminee' => ['Terminée', 'success'],
        'completed' => ['Terminée', 'success'],
        'open' => ['Ouverte', 'info'],
        'answered' => ['Répondue', 'warning'],
        'closed' => ['Clôturée', 'secondary'],
        'admin' => ['ADMIN', 'primary'],
        'manager' => ['MANAGER', 'info'],
        'employee' => ['EMPLOYEE', 'secondary'],
        'low' => ['Basse', 'secondary'],
        'medium' => ['Moyenne', 'info'],
        'high' => ['Haute', 'warning'],
        'urgent' => ['Urgente', 'danger'],
    ];

    [$label, $color] = $map[$key] ?? [ucfirst(str_replace('_', ' ', $key)), 'light'];
@endphp

<span {{ $attributes->merge(['class' => "badge rounded-pill text-bg-$color"]) }}>{{ $label }}</span>
