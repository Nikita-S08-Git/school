<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function principal()
    {
        return view('dashboard.principal');
    }

    public function student()
    {
        return view('dashboard.student');
    }

    public function teacher()
    {
        return view('dashboard.teacher');
    }

    public function office()
    {
        return view('dashboard.office');
    }

    public function accounts_staff()
    {
        return view('dashboard.accounts');
    }

    public function librarian()
    {
        return view('dashboard.librarian');
    }
}