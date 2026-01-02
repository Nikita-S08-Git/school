<?php

namespace App\Policies;

use App\Models\User\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'student_section', 'teacher', 'principal']);
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->hasAnyRole(['admin', 'student_section', 'principal'])) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            // Teachers can view students in their assigned classes/subjects
            return true; // TODO: Implement proper teacher-student assignment check
        }

        if ($user->hasRole('student')) {
            return $student->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'student_section']);
    }

    public function update(User $user, Student $student): bool
    {
        if ($user->hasAnyRole(['admin', 'student_section'])) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $student->user_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->hasAnyRole(['admin', 'student_section']);
    }
}