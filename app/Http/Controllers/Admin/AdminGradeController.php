<?php

namespace App\Http\Controllers\Admin;

use App\Models\GradeSystem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminGradeController extends Controller
{
    public function getGrade($score)
    {
        $grade = GradeSystem::getGrade($score);
        $status = $grade === 'F' ? 'Failed' : 'Passed';

        return response()->json([
            'grade' => $grade,
            'status' => $status,
        ]);
    }
}
