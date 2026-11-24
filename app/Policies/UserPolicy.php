<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Voir la liste des utilisateurs.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Consulter un utilisateur.
     */
    public function view(User $user, User $model): bool
    {
        // L'administrateur peut consulter tous les utilisateurs.
        if ($user->isAdmin()) {
            return true;
        }

        // Un utilisateur peut consulter son propre profil.
        if ($user->id === $model->id) {
            return true;
        }

        // Un manager peut consulter les membres de sa propre équipe.
        if ($user->isManager()) {
            return $user->team_id !== null
                && $user->team_id === $model->team_id;
        }

        return false;
    }

    /**
     * Ajouter un utilisateur.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Modifier un utilisateur.
     */
    public function update(User $user, User $model): bool
    {
        // L'administrateur peut modifier tous les utilisateurs.
        if ($user->isAdmin()) {
            return true;
        }

        // Un utilisateur peut modifier son propre profil.
        return $user->id === $model->id;
    }

    /**
     * Désactiver ou supprimer un utilisateur.
     */
    public function delete(User $user, User $model): bool
    {
        // Un administrateur peut désactiver les autres utilisateurs,
        // mais pas son propre compte.
        return $user->isAdmin() && $user->id !== $model->id;
    }

    public function restore(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }
}
