<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\CounselingPackage;
use App\Models\User;

class CounselingPackagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any CounselingPackage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CounselingPackage $counselingpackage): bool
    {
        return $user->checkPermissionTo('view CounselingPackage');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create CounselingPackage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CounselingPackage $counselingpackage): bool
    {
        return $user->checkPermissionTo('update CounselingPackage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CounselingPackage $counselingpackage): bool
    {
        return $user->checkPermissionTo('delete CounselingPackage');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any CounselingPackage');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CounselingPackage $counselingpackage): bool
    {
        return $user->checkPermissionTo('restore CounselingPackage');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any CounselingPackage');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, CounselingPackage $counselingpackage): bool
    {
        return $user->checkPermissionTo('replicate CounselingPackage');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder CounselingPackage');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CounselingPackage $counselingpackage): bool
    {
        return $user->checkPermissionTo('force-delete CounselingPackage');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any CounselingPackage');
    }
}
