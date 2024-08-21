<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Course;
use App\Models\Semester;
use App\Models\Department;
use App\Models\ScoreAudit;
use App\Models\StudentScore;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ApprovedScoresExport;

class AdminScoreApprovalController extends Controller
{
    public function index(Request $request)
    {
        $academicSessions = AcademicSession::orderBy('name', 'desc')->get();
        $semesters = Semester::all();

        $currentAcademicSession = AcademicSession::where('is_current', true)->first();
        $currentSemester = Semester::where('is_current', true)->first();

        $selectedSession = $request->input('academic_session_id', $currentAcademicSession ? $currentAcademicSession->id : $academicSessions->first()->id);
        $selectedSemester = $request->input('semester_id', $currentSemester ? $currentSemester->id : $semesters->first()->id);

        $selectedDepartment = $request->input('department_id');

        $query = StudentScore::where('status', 'pending')
            ->where('academic_session_id', $selectedSession)
            ->where('semester_id', $selectedSemester);

        if ($selectedDepartment) {
            $query->where('department_id', $selectedDepartment);
        }

        $pendingScores = $query->with(['student', 'course', 'teacher', 'department'])
            ->paginate(50);

        $departments = Department::all();

        return view('admin.score_approval.index', compact('pendingScores', 'academicSessions', 'semesters', 'selectedSession', 'selectedSemester', 'selectedDepartment', 'departments', 'currentAcademicSession', 'currentSemester'));
    }
    public function approveScore(Request $request)
    {
        $scoreIds = $request->input('score_ids', []);
        $comment = $request->input('comment');

        $scores = StudentScore::whereIn('id', $scoreIds)->get();

        foreach ($scores as $score) {
            $score->update(['status' => 'approved']);
            ScoreAudit::create([
                'student_score_id' => $score->id,
                'user_id' => auth()->id(),
                'action' => 'approved',
                'comment' => $comment,
            ]);
        }

        return redirect()->back()->with('success', 'Selected scores have been approved.');
    }

    public function reject(Request $request)
    {
        $scoreIds = $request->input('score_ids', []);
        $comment = $request->input('comment');

        $scores = StudentScore::whereIn('id', $scoreIds)->get();

        foreach ($scores as $score) {
            $score->update(['status' => 'rejected']);
            ScoreAudit::create([
                'student_score_id' => $score->id,
                'user_id' => auth()->id(),
                'action' => 'rejected',
                'comment' => $comment,
            ]);
        }

        return redirect()->back()->with('success', 'Selected scores have been rejected.');
    }

    public function export(Request $request)
    {
        $academicSessionId = $request->input('academic_session_id');
        $semesterId = $request->input('semester_id');

        $scores = StudentScore::with(['student.user', 'course', 'teacher.user'])
            ->where('academic_session_id', $academicSessionId)
            ->where('semester_id', $semesterId)
            ->get();

        $csvFileName = 'scores_export.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Student Name', 'Department', 'Course', 'Teacher', 'Assessment Score', 'Exam Score', 'Total Score', 'Grade', 'Status');

        $callback = function () use ($scores, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($scores as $score) {
                $row['Student Name'] = $score->student->user->fullName();
                $row['Department'] = $score->department->name;
                $row['Course'] = $score->course->title;
                $row['Teacher'] = $score->teacher->teacher_title . ' ' . $score->teacher->user->fullName();
                $row['Assessment Score'] = $score->assessment_score;
                $row['Exam Score'] = $score->exam_score;
                $row['Total Score'] = $score->total_score;
                $row['Grade'] = $score->grade;
                $row['Status'] = $score->status;

                fputcsv($file, array($row['Student Name'], $row['Department'], $row['Course'], $row['Teacher'], $row['Assessment Score'], $row['Exam Score'], $row['Total Score'], $row['Grade'], $row['Status']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    // NOT IMPLEMENTED YET
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        if ($request->hasFile('csv_file')) {
            $path = $request->file('csv_file')->getRealPath();
            $data = array_map('str_getcsv', file($path));

            // Remove header row
            array_shift($data);

            $errors = [];
            $imported = 0;

            foreach ($data as $row) {
                $studentName = $row[0];
                $department = $row[1];
                $courseName = $row[2];
                $teacherName = $row[3];
                $assessmentScore = $row[4];
                $examScore = $row[5];
                $totalScore = $row[6];
                $grade = $row[7];
                $status = $row[8];

                // Validate data
                if (!$this->validateImportRow($studentName, $department, $courseName, $teacherName, $assessmentScore, $examScore, $totalScore, $grade, $status)) {
                    $errors[] = "Invalid data in row: " . implode(', ', $row);
                    continue;
                }

                // Find or create records
                $student = User::where('name', $studentName)->first();
                $department = User::where('name', $department)->first();
                $course = Course::where('title', $courseName)->first();
                $teacher = User::where('name', $teacherName)->first();

                if (!$student || !$course || !$teacher || !$department) {
                    $errors[] = "Could not find student, course, or teacher for row: " . implode(', ', $row);
                    continue;
                }

                // Create or update StudentScore
                StudentScore::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                        'teacher_id' => $teacher->id,
                    ],
                    [
                        'assessment_score' => $assessmentScore,
                        'exam_score' => $examScore,
                        'total_score' => $totalScore,
                        'grade' => $grade,
                        'status' => $status,
                    ]
                );

                $imported++;
            }

            $message = "Imported $imported records successfully. ";
            if (count($errors) > 0) {
                $message .= count($errors) . " rows had errors.";
            }

            return redirect()->back()->with('success', $message)->with('import_errors', $errors);
        }

        return redirect()->back()->with('error', 'No file was uploaded.');
    }
    // this is validating the data for the import in the section
    private function validateImportRow($studentName, $department, $courseName, $teacherName, $assessmentScore, $examScore, $totalScore, $grade, $status)
    {
        if (empty($studentName) || empty($courseName) || empty($teacherName) || empty($department)) {
            return false;
        }

        if (!is_numeric($assessmentScore) || $assessmentScore < 0 || $assessmentScore > 40) {
            return false;
        }

        if (!is_numeric($examScore) || $examScore < 0 || $examScore > 60) {
            return false;
        }

        if (!is_numeric($totalScore) || $totalScore != $assessmentScore + $examScore) {
            return false;
        }

        if (!in_array($grade, ['A', 'B', 'C', 'D', 'E', 'F'])) {
            return false;
        }

        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return false;
        }

        return true;
    }





    // view approved exam scores
    public function approvedScores(Request $request)
    {
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

    public function exportApprovedScores(Request $request)
    {
        $academicSessionId = $request->input('academic_session_id');
        $semesterId = $request->input('semester_id');
        $approvedScores = StudentScore::with(['student.user', 'course', 'teacher.user'])
            ->where('academic_session_id', $academicSessionId)
            ->where('semester_id', $semesterId)
            ->where('status', 'approved')
            ->get();

        return Excel::download(new \App\Exports\ApprovedScoresExport($approvedScores), 'approved_scores.xlsx');
    }






    public function rejectedScores(Request $request)
    {
        $academicSessions = AcademicSession::orderBy('name', 'desc')->get();
        $semesters = Semester::all();

        $currentAcademicSession = AcademicSession::where('is_current', true)->first();
        $currentSemester = Semester::where('is_current', true)->first();

        $selectedSession = $request->input('academic_session_id', $currentAcademicSession ? $currentAcademicSession->id : $academicSessions->first()->id);
        $selectedSemester = $request->input('semester_id', $currentSemester ? $currentSemester->id : $semesters->first()->id);

        $selectedDepartment = $request->input('department_id');

        $rejectedScores = StudentScore::where('status', 'rejected')
            ->where('academic_session_id', $selectedSession)
            ->where('semester_id', $selectedSemester)
            ->when($selectedDepartment, function ($query) use ($selectedDepartment) {
                return $query->where('department_id', $selectedDepartment);
            })
            ->with(['student', 'course', 'teacher', 'department'])
            ->paginate(50);

        $departments = Department::all();

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
}
