<?php

namespace App\Http\Controllers\Admin;

use League\Csv\Reader;
use League\Csv\Writer;
use App\Models\Semester;
use App\Models\Department;
use App\Models\ScoreAudit;
use App\Models\StudentScore;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AdminRejectedScoreController extends Controller
{

    public function rejectedScores(Request $request)
    {
        $academicSessions = AcademicSession::orderBy('name', 'desc')->get();
        $semesters = Semester::all();
        $departments = Department::all();

        $currentAcademicSession = AcademicSession::where('is_current', true)->first();
        $currentSemester = Semester::where('is_current', true)->first();

        $selectedSession = $request->input('academic_session_id', $currentAcademicSession ? $currentAcademicSession->id : $academicSessions->first()->id);
        $selectedSemester = $request->input('semester_id', $currentSemester ? $currentSemester->id : $semesters->first()->id);
        $selectedDepartment = $request->input('department_id');

        $rejectedScores = StudentScore::where('status', 'rejected')
            ->when($selectedSession, function ($query) use ($selectedSession) {
                return $query->where('academic_session_id', $selectedSession);
            })
            ->when($selectedSemester, function ($query) use ($selectedSemester) {
                return $query->where('semester_id', $selectedSemester);
            })
            ->when($selectedDepartment, function ($query) use ($selectedDepartment) {
                return $query->where('department_id', $selectedDepartment);
            })
            ->with(['student.user', 'course', 'teacher.user', 'department'])
            ->paginate(50);

        return view('admin.score_approval.rejected_result', compact(
            'rejectedScores',
            'academicSessions',
            'semesters',
            'selectedSession',
            'selectedSemester',
            'selectedDepartment',
            'departments',
            'currentAcademicSession',
            'currentSemester'
        ));
    }

    public function revertRejection(Request $request, StudentScore $score)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $score->update(['status' => 'pending']);

        ScoreAudit::create([
            'student_score_id' => $score->id,
            'user_id' => auth()->id(),
            'action' => 'reverted_rejection',
            'comment' => $request->input('comment', 'Single revert action'),
        ]);

        return redirect()->back()->with('success', 'The rejection has been reverted.');
    }


    public function bulkRevertRejection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'score_ids' => 'required|array',
            'score_ids.*' => 'exists:student_scores,id',
            'comment' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $scoreIds = $request->input('score_ids', []);
        $comment = $request->input('comment', 'Bulk revert action');

        if (empty($scoreIds)) {
            return redirect()->back()->with('error', 'No scores were selected for reversion.');
        }

        StudentScore::whereIn('id', $scoreIds)->update(['status' => 'pending']);

        foreach ($scoreIds as $scoreId) {
            ScoreAudit::create([
                'student_score_id' => $scoreId,
                'user_id' => auth()->id(),
                'action' => 'reverted_rejection',
                'comment' => $comment,
            ]);
        }

        return redirect()->back()->with('success', 'The selected rejected scores have been reverted.');
    }

    public function bulkAcceptRejection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'score_ids' => 'required|array',
            'score_ids.*' => 'exists:student_scores,id',
            'comment' => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $scoreIds = $request->input('score_ids', []);
        $comment = $request->input('comment', 'Bulk accept action');

        if (empty($scoreIds)) {
            return redirect()->back()->with('error', 'No scores were selected for acceptance.');
        }

        StudentScore::whereIn('id', $scoreIds)->update(['status' => 'approved']);

        foreach ($scoreIds as $scoreId) {
            ScoreAudit::create([
                'student_score_id' => $scoreId,
                'user_id' => auth()->id(),
                'action' => 'accepted_rejection',
                'comment' => $comment,
            ]);
        }

        return redirect()->back()->with('success', 'The selected rejected scores have been accepted.');
    }


    public function exportRejectedScores(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $scores = StudentScore::where('status', 'rejected')
            ->where('academic_session_id', $request->academic_session_id)
            ->where('semester_id', $request->semester_id)
            ->with(['student.user', 'course', 'teacher.user', 'department'])
            ->get();

        $csv = Writer::createFromString('');
        $csv->insertOne(['Student','Mat ID', 'Course', 'Department', 'Lecturer', 'Assessment', 'Exam', 'Total', 'Grade']);

        foreach ($scores as $score) {
            $csv->insertOne([
                $score->student->user->fullName(),
                $score->student->matric_number,
                $score->course->title,
                $score->department->name,
                $score->teacher->teacher_title . ' ' . $score->teacher->user->fullName(),
                $score->assessment_score,
                $score->exam_score,
                $score->total_score,
                $score->grade,
            ]);
        }

        $filename = 'rejected_scores_' . date('Y-m-d') . '.csv';
        return response($csv->getContent())
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }

    public function importRejectedScores(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $file = $request->file('csv_file');
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        foreach ($records as $record) {
            // Assuming the CSV columns match the database columns
            StudentScore::updateOrCreate(
                [
                    'student_id' => $record['student_id'],
                    'course_id' => $record['course_id'],
                    'academic_session_id' => $record['academic_session_id'],
                    'semester_id' => $record['semester_id'],
                ],
                [
                    'department_id' => $record['department_id'],
                    'teacher_id' => $record['teacher_id'],
                    'assessment_score' => $record['assessment_score'],
                    'exam_score' => $record['exam_score'],
                    'total_score' => $record['total_score'],
                    'grade' => $record['grade'],
                    'status' => 'rejected',
                ]
            );
        }

        return redirect()->back()->with('success', 'CSV file has been imported successfully.');
    }
}
