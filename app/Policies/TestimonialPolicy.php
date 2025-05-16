<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Testimonial;
use App\Models\User;

class TestimonialPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Testimonial');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Testimonial $testimonial): bool
    {
        return $user->checkPermissionTo('view Testimonial');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Testimonial');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Testimonial $testimonial): bool
    {
        return $user->checkPermissionTo('update Testimonial');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Testimonial $testimonial): bool
    {
        return $user->checkPermissionTo('delete Testimonial');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Testimonial');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Testimonial $testimonial): bool
    {
        return $user->checkPermissionTo('restore Testimonial');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Testimonial');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Testimonial $testimonial): bool
    {
        return $user->checkPermissionTo('replicate Testimonial');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Testimonial');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Testimonial $testimonial): bool
    {
        return $user->checkPermissionTo('force-delete Testimonial');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Testimonial');
    }
}
