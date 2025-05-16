<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Career;
use App\Models\User;

class CareerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Career');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Career $career): bool
    {
        return $user->checkPermissionTo('view Career');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Career');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Career $career): bool
    {
        return $user->checkPermissionTo('update Career');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Career $career): bool
    {
        return $user->checkPermissionTo('delete Career');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Career');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Career $career): bool
    {
        return $user->checkPermissionTo('restore Career');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Career');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Career $career): bool
    {
        return $user->checkPermissionTo('replicate Career');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Career');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Career $career): bool
    {
        return $user->checkPermissionTo('force-delete Career');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Career');
    }
}
