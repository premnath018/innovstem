<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Option;
use App\Models\User;

class OptionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Option');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Option $option): bool
    {
        return $user->checkPermissionTo('view Option');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Option');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Option $option): bool
    {
        return $user->checkPermissionTo('update Option');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Option $option): bool
    {
        return $user->checkPermissionTo('delete Option');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Option');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Option $option): bool
    {
        return $user->checkPermissionTo('restore Option');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Option');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Option $option): bool
    {
        return $user->checkPermissionTo('replicate Option');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Option');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Option $option): bool
    {
        return $user->checkPermissionTo('force-delete Option');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Option');
    }
}
