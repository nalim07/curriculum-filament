<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TkAchivementGrade;
use Illuminate\Auth\Access\HandlesAuthorization;

class TkAchivementGradePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_teacher::pg::kg::tk::achivement::grade');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TkAchivementGrade $tkAchivementGrade): bool
    {
        return $user->can('view_teacher::pg::kg::tk::achivement::grade');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_teacher::pg::kg::tk::achivement::grade');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TkAchivementGrade $tkAchivementGrade): bool
    {
        return $user->can('update_teacher::pg::kg::tk::achivement::grade');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TkAchivementGrade $tkAchivementGrade): bool
    {
        return $user->can('delete_teacher::pg::kg::tk::achivement::grade');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_teacher::pg::kg::tk::achivement::grade');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, TkAchivementGrade $tkAchivementGrade): bool
    {
        return $user->can('force_delete_teacher::pg::kg::tk::achivement::grade');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_teacher::pg::kg::tk::achivement::grade');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, TkAchivementGrade $tkAchivementGrade): bool
    {
        return $user->can('restore_teacher::pg::kg::tk::achivement::grade');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_teacher::pg::kg::tk::achivement::grade');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, TkAchivementGrade $tkAchivementGrade): bool
    {
        return $user->can('replicate_teacher::pg::kg::tk::achivement::grade');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_teacher::pg::kg::tk::achivement::grade');
    }
}
