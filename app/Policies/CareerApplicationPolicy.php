<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\CareerApplication;
use App\Models\User;

class CareerApplicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any CareerApplication');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CareerApplication $careerapplication): bool
    {
        return $user->checkPermissionTo('view CareerApplication');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create CareerApplication');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CareerApplication $careerapplication): bool
    {
        return $user->checkPermissionTo('update CareerApplication');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CareerApplication $careerapplication): bool
    {
        return $user->checkPermissionTo('delete CareerApplication');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any CareerApplication');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CareerApplication $careerapplication): bool
    {
        return $user->checkPermissionTo('restore CareerApplication');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any CareerApplication');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, CareerApplication $careerapplication): bool
    {
        return $user->checkPermissionTo('replicate CareerApplication');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder CareerApplication');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CareerApplication $careerapplication): bool
    {
        return $user->checkPermissionTo('force-delete CareerApplication');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any CareerApplication');
    }
}
