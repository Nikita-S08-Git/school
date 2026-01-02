<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Fee;
use App\Models\User;

class DashboardController extends Controller
{
    public function student()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        
        $data = [
            'student' => $student,
            'stats' => [
                'attendance' => 85,
                'feesPaid' => 15000,
                'pendingFees' => 5000,
                'booksIssued' => 3
            ],
            'recentActivities' => [
                [
                    'id' => 1,
                    'icon' => 'bi bi-cash-stack text-success',
                    'title' => 'Fee Payment Received',
                    'description' => 'Monthly fee payment of ₹2,500 received',
                    'date' => '2 days ago'
                ],
                [
                    'id' => 2,
                    'icon' => 'bi bi-book text-primary',
                    'title' => 'Book Issued',
                    'description' => 'Mathematics Textbook issued from library',
                    'date' => '1 week ago'
                ]
            ]
        ];
        
        return view('dashboards.student', $data);
    }
    
    public function teacher()
    {
        $user = Auth::user();
        
        $data = [
            'teacher' => $user,
            'stats' => [
                'totalStudents' => 180,
                'totalClasses' => 5,
                'todayClasses' => 3,
                'pendingAssignments' => 7
            ],
            'todaySchedule' => [
                [
                    'time' => '09:00 - 10:00',
                    'subject' => 'Mathematics',
                    'class' => 'Class 10-A',
                    'room' => 'Room 101'
                ],
                [
                    'time' => '11:00 - 12:00',
                    'subject' => 'Physics',
                    'class' => 'Class 10-B',
                    'room' => 'Room 204'
                ]
            ]
        ];
        
        return view('dashboards.teacher', $data);
    }
    
    public function principal()
    {
        $data = [
            'stats' => [
                'totalStudents' => Student::count(),
                'totalTeachers' => User::role('teacher')->count(),
                'totalStaff' => User::whereHas('roles', function($q) {
                    $q->whereIn('name', ['teacher', 'accounts_staff', 'librarian']);
                })->count(),
                'pendingFees' => Fee::where('status', 'pending')->count()
            ],
            'recentActivities' => [
                [
                    'title' => 'New Student Admission',
                    'description' => '5 new students admitted today',
                    'time' => '2 hours ago',
                    'icon' => 'bi bi-person-plus text-success'
                ],
                [
                    'title' => 'Fee Collection',
                    'description' => '₹50,000 collected today',
                    'time' => '4 hours ago',
                    'icon' => 'bi bi-cash-stack text-primary'
                ]
            ]
        ];
        
        return view('dashboards.principal', $data);
    }
    
    public function accountant()
    {
        $data = [
            'stats' => [
                'todayCollection' => 25000,
                'pendingFees' => 88,
                'totalExpenses' => 15000,
                'monthlyTarget' => 500000
            ],
            'recentTransactions' => [
                [
                    'student' => 'John Doe',
                    'amount' => 2500,
                    'type' => 'Fee Payment',
                    'date' => today()->format('Y-m-d'),
                    'status' => 'completed'
                ],
                [
                    'student' => 'Jane Smith',
                    'amount' => 1800,
                    'type' => 'Partial Payment',
                    'date' => today()->subDay()->format('Y-m-d'),
                    'status' => 'completed'
                ]
            ]
        ];
        
        return view('dashboards.accountant', $data);
    }
}