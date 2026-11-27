@extends('layouts.admin.template')

@section('title', 'Problèmes ouverts')

@section('content')
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 fw-bold mb-1">Problèmes ouverts</h1>
            <p class="text-secondary mb-0">
                Liste des problèmes signalés sur les tâches.
            </p>
        </div>

        <a href="{{ route('manager.dashboard') }}" class="btn btn-outline-secondary">
            Retour au tableau de bord
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h2 class="h5 fw-bold mb-0">
                Problèmes à traiter
                <span class="badge bg-warning text-dark ms-2">{{ $issues->total() }}</span>
            </h2>
        </div>

        <div class="card-body p-0">
            @if ($issues->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-check-circle text-success fs-1"></i>
                    <h5 class="mt-3">Aucun problème ouvert</h5>
                    <p class="text-secondary mb-0">
                        Il n'y a actuellement aucun problème à traiter.
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Tâche</th>
                                <th>Projet</th>
                                <th>Employé assigné</th>
                                <th>Statut du problème</th>
                                <th>Date de signalement</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($issues as $issue)
                                <tr>
                                    <td class="ps-3 fw-semibold">
                                        {{ $issue->task?->title ?? 'Tâche supprimée' }}
                                    </td>

                                    <td>
                                        {{ $issue->task?->project?->name ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $issue->task?->assignee?->name ?? 'Non assigné' }}
                                    </td>

                                    <td>
                                        @if ($issue->status === 'pending')
                                            <span class="badge bg-warning text-dark">En attente</span>
                                        @elseif ($issue->status === 'open')
                                            <span class="badge bg-danger">Ouvert</span>
                                        @else
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($issue->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $issue->created_at?->format('d/m/Y H:i') ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($issues->hasPages())
            <div class="card-footer bg-white">
                {{ $issues->links() }}
            </div>
        @endif
    </div>
@endsection