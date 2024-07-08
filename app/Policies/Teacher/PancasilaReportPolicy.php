<?php

namespace App\Policies\Teacher;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PancasilaReportPolicy
{
    use HandlesAuthorization;

    /**
     * Check if the user has the necessary permission or is associated with a classSchool.
     */
    protected function hasAccess(User $user, string $permission): bool
    {
        // Check if the user has the specified permission
        if ($user->can($permission)) {
            return true;
        }

        // Ensure the user's associated teacher is present in a classSchool
        return $user->employee
            && $user->employee->teacher
            && $user->employee->teacher->classSchool()->exists();
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasAccess($user, 'view_any_teacher::print::semester');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return $this->hasAccess($user, 'view_teacher::print::semester');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasAccess($user, 'create_teacher::print::semester');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $this->hasAccess($user, 'update_teacher::print::semester');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $this->hasAccess($user, 'delete_teacher::print::semester');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $this->hasAccess($user, 'delete_any_teacher::print::semester');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user): bool
    {
        return $this->hasAccess($user, '{{ ForceDelete }}');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $this->hasAccess($user, '{{ ForceDeleteAny }}');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user): bool
    {
        return $this->hasAccess($user, '{{ Restore }}');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $this->hasAccess($user, '{{ RestoreAny }}');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user): bool
    {
        return $this->hasAccess($user, '{{ Replicate }}');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $this->hasAccess($user, '{{ Reorder }}');
    }
}
