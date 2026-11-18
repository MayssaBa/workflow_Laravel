@props(['status'])

@php
    // Accepte une chaîne ou un enum (TaskStatus, TaskPriority...) : seule la valeur compte.
    $key = $status instanceof \BackedEnum ? (string) $status->value : (string) $status;
    $key = strtolower(trim($key));

    $map = [
        // statuts de tâche
        'todo' => ['À faire', 'secondary'], 'to_do' => ['À faire', 'secondary'], 'a_faire' => ['À faire', 'secondary'],
        'in_progress' => ['En cours', 'primary'], 'en_cours' => ['En cours', 'primary'],
        'blocked' => ['Bloquée', 'danger'], 'bloquee' => ['Bloquée', 'danger'],
        'done' => ['Terminée', 'success'], 'terminee' => ['Terminée', 'success'], 'completed' => ['Terminée', 'success'],
        // statuts de demande
        'open' => ['Ouverte', 'info'], 'answered' => ['Répondue', 'warning'], 'closed' => ['Clôturée', 'secondary'],
        // priorités
        'low' => ['Basse', 'secondary'], 'medium' => ['Moyenne', 'info'],
        'high' => ['Haute', 'warning'], 'urgent' => ['Urgente', 'danger'],
    ];

    [$label, $color] = $map[$key] ?? [ucfirst(str_replace('_', ' ', $key)), 'light'];
@endphp

<span {{ $attributes->merge(['class' => "badge text-bg-$color"]) }}>{{ $label }}</span>
