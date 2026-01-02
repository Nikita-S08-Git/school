<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Academic\Program;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::select('id', 'name')->get();
        return response()->json(['data' => $programs]);
    }
}