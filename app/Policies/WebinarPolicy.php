<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Webinar;
use App\Models\User;

class WebinarPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Webinar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Webinar $webinar): bool
    {
        return $user->checkPermissionTo('view Webinar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Webinar');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Webinar $webinar): bool
    {
        return $user->checkPermissionTo('update Webinar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Webinar $webinar): bool
    {
        return $user->checkPermissionTo('delete Webinar');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Webinar');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Webinar $webinar): bool
    {
        return $user->checkPermissionTo('restore Webinar');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Webinar');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Webinar $webinar): bool
    {
        return $user->checkPermissionTo('replicate Webinar');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Webinar');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Webinar $webinar): bool
    {
        return $user->checkPermissionTo('force-delete Webinar');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Webinar');
    }
}
