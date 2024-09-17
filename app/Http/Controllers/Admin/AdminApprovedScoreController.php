<?php

namespace App\Http\Controllers\Admin;

use League\Csv\Reader;
use League\Csv\Writer;
use App\ScoreAuditable;
use App\Models\Semester;
use App\Models\Department;
use App\Models\ScoreAudit;
use App\Models\StudentScore;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class AdminApprovedScoreController extends Controller
{
    use ScoreAuditable;


    // view approved exam scores
    public function approvedScores(Request $request)
    {
        // dd('here ..');
        $academicSessions = AcademicSession::orderBy('name', 'desc')->get();
        $semesters = Semester::all();

        $currentAcademicSession = AcademicSession::where('is_current', true)->first();
        $currentSemester = Semester::where('is_current', true)->first();

        $selectedSession = $request->input('academic_session_id', $currentAcademicSession ? $currentAcademicSession->id : $academicSessions->first()->id);
        $selectedSemester = $request->input('semester_id', $currentSemester ? $currentSemester->id : $semesters->first()->id);

        $selectedDepartment = $request->input('department_id');

        $approvedScores = StudentScore::where('status', 'approved')
            ->where('academic_session_id', $selectedSession)
            ->where('semester_id', $selectedSemester)
            ->when($selectedDepartment, function ($query) use ($selectedDepartment) {
                return $query->where('department_id', $selectedDepartment);
            })
            ->with(['student', 'course', 'teacher', 'department'])
            ->paginate(50);

        $departments = Department::all();

        return view('admin.score_approval.approved_result', compact(
            'approvedScores',
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

    //revert the approved score to pending
    public function revertApproval(Request $request, StudentScore $score)
    {
        $oldValue = $score->getOriginal();

        // Directly update the database
        StudentScore::where('id', $score->id)->update(['status' => 'pending']);

        // Refresh the model to get updated attributes
        $score->refresh();
        $newValue = $score->getAttributes();

        $this->auditScore(
            $score->id,
            'Revert',
            $request->input('comment') ?? 'Reverted Approval',
            $oldValue,
            $newValue
        );

        return redirect()->back()->with('success', 'The approval has been reverted.');
    }
    public function bulkRevertApproval(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'score_ids' => 'required|array|min:1',
            'score_ids.*' => 'exists:student_scores,id',
            'comment' => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $scoreIds = $request->input('score_ids');
        $comment = $request->input('comment');

        foreach ($scoreIds as $scoreId) {
            $score = StudentScore::findOrFail($scoreId);
            $oldValue = $score->getOriginal();

            // Directly update the database
            StudentScore::where('id', $scoreId)->update(['status' => 'pending']);

            // Refresh the model to get updated attributes
            $score->refresh();
            $newValue = $score->getAttributes();

            $this->auditScore($score->id, 'Revert', $comment ?? "Reverted Approval in bulk", $oldValue, $newValue);
        }

        return redirect()->back()->with('success', 'The selected approved scores have been reverted.');
    }

    public function exportApprovedScores(Request $request)
    {
        $academicSessionId = $request->input('academic_session_id');
        $semesterId = $request->input('semester_id');
        $approvedScores = StudentScore::with(['student.user', 'course', 'teacher.user', 'department'])
            ->where('academic_session_id', $academicSessionId)
            ->where('semester_id', $semesterId)
            ->where('status', 'approved')
            ->get();

        $csv = Writer::createFromString('');
        $csv->insertOne(['Student', 'Matric Number', 'Course', 'Department', 'Lecturer', 'Assessment', 'Exam', 'Total', 'Grade']);

        foreach ($approvedScores as $score) {
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
            $this->auditScore($score->id, 'Export', 'Scores exported to CSV', null, [
                'academic_session_id' => $request->input('academic_session_id'),
                'semester_id' => $request->input('semester_id'),
            ]);
        }

        $filename = 'approved_scores_' . date('Y-m-d') . '.csv';



        return response($csv->getContent())
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }



    public function importApprovedScores(Request $request)
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
            $oldScore = StudentScore::where([
                'student_id' => $record['student_id'],
                'course_id' => $record['course_id'],
                'academic_session_id' => $record['academic_session_id'],
                'semester_id' => $record['semester_id'],
            ])->first();

            $oldValue = $oldScore ? $oldScore->getAttributes() : null;

            $score = StudentScore::updateOrCreate(
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
                    'status' => 'approved',
                ]
            );

            $this->auditScore($score->id, 'Import', 'Score imported from CSV', $oldValue, $score->getAttributes());
        }

        return redirect()->back()->with('success', 'CSV file has been imported successfully.');
    }
}
