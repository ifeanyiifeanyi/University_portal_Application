<?php

namespace App\Helpers;

use App\Models\AcademicSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class ActivityLogHelper
{
    public static function logSemesterActivity(string $action, Model $semester)
    {
        activity()
            ->performedOn($semester)
            ->causedBy(Auth::user())
            ->withProperties([
                'name' => $semester->name,
                'season' => $semester->season,
                'start_date' => $semester->start_date,
                'end_date' => $semester->end_date,
                'academic_session_id' => $semester->academic_session_id,
                'is_current' => $semester->is_current,
            ])
            ->log($action . ' semester');
    }

    public static function logAcademicSessionActivity(string $action, AcademicSession $academicSession)
    {
        activity()
            ->performedOn($academicSession)
            ->causedBy(Auth::user())
            ->withProperties([
                'name' => $academicSession->name,
                'start_date' => date('jS F Y', strtotime($academicSession->start_date)),
                'end_date' => date('jS F Y', strtotime($academicSession->end_date)),
                'is_current' => $academicSession->is_current,
            ])
            ->log($action . ' academic session');
    }
}
