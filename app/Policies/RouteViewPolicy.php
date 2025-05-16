<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\RouteView;
use App\Models\User;

class RouteViewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any RouteView');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RouteView $routeview): bool
    {
        return $user->checkPermissionTo('view RouteView');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create RouteView');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RouteView $routeview): bool
    {
        return $user->checkPermissionTo('update RouteView');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RouteView $routeview): bool
    {
        return $user->checkPermissionTo('delete RouteView');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any RouteView');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RouteView $routeview): bool
    {
        return $user->checkPermissionTo('restore RouteView');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any RouteView');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, RouteView $routeview): bool
    {
        return $user->checkPermissionTo('replicate RouteView');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder RouteView');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RouteView $routeview): bool
    {
        return $user->checkPermissionTo('force-delete RouteView');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any RouteView');
    }
}
