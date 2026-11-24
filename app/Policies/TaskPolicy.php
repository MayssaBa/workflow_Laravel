<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Voir la liste des tâches.
     */
    public function viewAny(User $user): bool
    {
        return $user->isManager() || $user->isEmployee();
    }

    /**
     * Consulter une tâche.
     */
    public function view(User $user, Task $task): bool
    {
        // Le manager peut consulter toutes les tâches.
        if ($user->isManager()) {
            return true;
        }

        // L'employé peut consulter uniquement ses propres tâches.
        return $user->isEmployee()
            && $task->assigned_to === $user->id;
    }

    /**
     * Créer une tâche.
     */
    public function create(User $user): bool
    {
        return $user->isManager();
    }

    /**
     * Modifier les informations générales d'une tâche.
     * Seul le manager peut le faire.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->isManager();
    }

    /**
     * Modifier uniquement le statut d'une tâche.
     */
    public function updateStatus(User $user, Task $task): bool
    {
        // Le manager peut modifier le statut de toutes les tâches.
        if ($user->isManager()) {
            return true;
        }

        // L'employé peut modifier le statut de ses propres tâches.
        return $user->isEmployee()
            && $task->assigned_to === $user->id;
    }

    /**
     * Supprimer une tâche.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->isManager();
    }

    /**
     * Restaurer une tâche supprimée logiquement.
     */
    public function restore(User $user, Task $task): bool
    {
        return $user->isManager();
    }

    /**
     * Suppression définitive.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}
