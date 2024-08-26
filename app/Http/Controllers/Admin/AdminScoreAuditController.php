<?php

namespace App\Http\Controllers\Admin;

use App\Models\Semester;
use App\Models\ScoreAudit;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Exports\ScoreAuditsExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;

class AdminScoreAuditController extends Controller
{
   

    public function index(Request $request)
    {
        $academicSessions = Cache::remember('academic_sessions', 60 * 24, function () {
            return AcademicSession::orderBy('name', 'desc')->get();
        });

        $semesters = Cache::remember('semesters', 60 * 24, function () {
            return Semester::all();
        });

        $query = ScoreAudit::with([
            'studentScore.student.user',
            'studentScore.course',
            'user',
            'studentScore.academicSession',
            'studentScore.semester'
        ]);

        if ($request->filled('academic_session_id')) {
            $query->whereHas('studentScore', function ($q) use ($request) {
                $q->where('academic_session_id', $request->academic_session_id);
            });
        }

        if ($request->filled('semester_id')) {
            $query->whereHas('studentScore', function ($q) use ($request) {
                $q->where('semester_id', $request->semester_id);
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('student')) {
            $query->whereHas('studentScore.student.user', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->student . '%');
            });
        }

        if ($request->filled('course')) {
            $query->whereHas('studentScore.course', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->course . '%');
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->user . '%');
            });
        }

        $perPage = 20;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;

        $totalAudits = $query->count();

        $audits = $query->latest()->offset($offset)->limit($perPage)->get();

        $groupedAudits = $audits->groupBy(function ($audit) {
            return $audit->studentScore->academicSession->name . ' - ' . $audit->studentScore->semester->name;
        });

        $audits = new \Illuminate\Pagination\LengthAwarePaginator(
            $groupedAudits,
            $totalAudits,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.scoreAudits.index', compact('audits', 'academicSessions', 'semesters'));
    }

    // public function export(Request $request)
    // {
    //     return Excel::download(new ScoreAuditsExport($request), 'score_audits.xlsx');
    // }

    public function export(Request $request)
    {
        // return Excel::download(new ScoreAuditsExport($request), 'score_audits.xlsx');
    }

}
