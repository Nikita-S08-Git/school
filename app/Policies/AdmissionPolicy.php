<?php

namespace App\Policies;

use App\Models\Academic\Admission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdmissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'student_section', 'principal']);
    }

    public function view(User $user, Admission $admission): bool
    {
        if ($user->hasAnyRole(['admin', 'student_section', 'principal'])) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $admission->email === $user->email;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return true; // Public endpoint
    }

    public function uploadDocuments(User $user, Admission $admission): bool
    {
        if ($user->hasAnyRole(['admin', 'student_section'])) {
            return true;
        }

        return $admission->email === $user->email;
    }

    public function verifyDocuments(User $user, Admission $admission): bool
    {
        return $user->hasRole('student_section');
    }

    public function verify(User $user, Admission $admission): bool
    {
        return $user->hasRole('student_section');
    }

    public function update(User $user, Admission $admission): bool
    {
        if ($admission->status !== 'applied') {
            return false;
        }

        if ($user->hasAnyRole(['admin', 'student_section'])) {
            return true;
        }

        return $admission->email === $user->email;
    }

    public function delete(User $user, Admission $admission): bool
    {
        return $user->hasRole('admin');
    }
}