<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\ClassLevel;
use App\Models\User;

class ClassLevelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any ClassLevel');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ClassLevel $classlevel): bool
    {
        return $user->checkPermissionTo('view ClassLevel');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create ClassLevel');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ClassLevel $classlevel): bool
    {
        return $user->checkPermissionTo('update ClassLevel');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ClassLevel $classlevel): bool
    {
        return $user->checkPermissionTo('delete ClassLevel');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any ClassLevel');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ClassLevel $classlevel): bool
    {
        return $user->checkPermissionTo('restore ClassLevel');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any ClassLevel');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, ClassLevel $classlevel): bool
    {
        return $user->checkPermissionTo('replicate ClassLevel');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder ClassLevel');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ClassLevel $classlevel): bool
    {
        return $user->checkPermissionTo('force-delete ClassLevel');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any ClassLevel');
    }
}
