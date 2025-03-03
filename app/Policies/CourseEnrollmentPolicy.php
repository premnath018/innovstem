<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\CourseEnrollment;
use App\Models\User;

class CourseEnrollmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any CourseEnrollment');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CourseEnrollment $courseenrollment): bool
    {
        return $user->checkPermissionTo('view CourseEnrollment');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create CourseEnrollment');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CourseEnrollment $courseenrollment): bool
    {
        return $user->checkPermissionTo('update CourseEnrollment');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CourseEnrollment $courseenrollment): bool
    {
        return $user->checkPermissionTo('delete CourseEnrollment');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any CourseEnrollment');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CourseEnrollment $courseenrollment): bool
    {
        return $user->checkPermissionTo('restore CourseEnrollment');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any CourseEnrollment');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, CourseEnrollment $courseenrollment): bool
    {
        return $user->checkPermissionTo('replicate CourseEnrollment');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder CourseEnrollment');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CourseEnrollment $courseenrollment): bool
    {
        return $user->checkPermissionTo('force-delete CourseEnrollment');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any CourseEnrollment');
    }
}
