<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\WebinarAttendance;
use App\Models\User;

class WebinarAttendancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any WebinarAttendance');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WebinarAttendance $webinarattendance): bool
    {
        return $user->checkPermissionTo('view WebinarAttendance');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create WebinarAttendance');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WebinarAttendance $webinarattendance): bool
    {
        return $user->checkPermissionTo('update WebinarAttendance');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WebinarAttendance $webinarattendance): bool
    {
        return $user->checkPermissionTo('delete WebinarAttendance');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any WebinarAttendance');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WebinarAttendance $webinarattendance): bool
    {
        return $user->checkPermissionTo('restore WebinarAttendance');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any WebinarAttendance');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, WebinarAttendance $webinarattendance): bool
    {
        return $user->checkPermissionTo('replicate WebinarAttendance');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder WebinarAttendance');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WebinarAttendance $webinarattendance): bool
    {
        return $user->checkPermissionTo('force-delete WebinarAttendance');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any WebinarAttendance');
    }
}
