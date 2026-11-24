<?php

namespace App\Policies;

use App\Enums\RequestStatus;
use App\Models\InternalRequest;
use App\Models\User;

class RequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isManager() || $user->isEmployee();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InternalRequest $internalRequest): bool
    {
        return $user->id === $internalRequest->sender_id
            || $user->id === $internalRequest->recipient_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isEmployee() || $user->isManager();
    }

    /**
     * Determine whether the user can reply to the model.
     */
    public function reply(User $user, InternalRequest $internalRequest): bool
    {
        if ($internalRequest->status === RequestStatus::CLOSED) {
            return false;
        }

        if ($user->id === $internalRequest->recipient_id) {
            return true;
        }

        return $user->id === $internalRequest->sender_id
            && $internalRequest->replies()->exists();
    }

    /**
     * Determine whether the user can close the model.
     */
    public function close(User $user, InternalRequest $internalRequest): bool
    {
        return $user->id === $internalRequest->recipient_id
            && $internalRequest->status !== RequestStatus::CLOSED;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, InternalRequest $internalRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InternalRequest $internalRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, InternalRequest $internalRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, InternalRequest $internalRequest): bool
    {
        return false;
    }
}
