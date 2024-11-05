<?php

namespace App\Http\Controllers\Teacher;

use App\Models\User;
use League\Csv\Reader;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\GpaRecord;
use App\Models\GradeSystem;
use App\Models\StudentScore;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Models\CourseEnrollment;
use App\Models\TeacherAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TeacherCoursesController extends Controller
{
    protected $authService;

    
    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService){

        $this->authService = $authService;
    }
    public function courses(){
         // get the teachers details
         $teacher = Teacher::with(['user'])->where('user_id',$this->authService->user()->id)->first();
         // get the department
         $coursesassigned = TeacherAssignment::with(['course','department','semester','academicSession'])->where('teacher_id',$teacher->id)->get();
         return view('teacher.courses.courses',[
            'coursesassigned'=>$coursesassigned
        ]);
    }

public function students($courseId)
{
    $teacher = Teacher::where('user_id', $this->authService->user()->id)->first();

    // Eager load the studentScore with additional constraints
    $students = CourseEnrollment::with(['student.user', 'course', 'department', 'studentScore' => function ($query) use ($teacher) {
        $query->where('teacher_id', $teacher->id);
    }])
    ->where('course_id', $courseId)
    ->whereHas('semesterCourseRegistration', function ($query) {
        $query->where('status', 'approved');
    })
    ->get();

    return view('teacher.courses.enrolledstudents', [
        'students' => $students,
        'courseId' => $courseId,
    ]);
}

    public function getGrade($score)
    {
        $grade = GradeSystem::getGrade($score);
        $status = $grade === 'F' ? 'Failed' : 'Passed';

        return response()->json([
            'grade' => $grade,
            'status' => $status,
        ]);
    }



    public function exportassessment($courseId){

        $teacher = Teacher::where('user_id', $this->authService->user()->id)->first();

        // Eager load the studentScore with additional constraints
        $exportaccess = CourseEnrollment::with(['student.user', 'course', 'department', 'studentScore' => function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        }])
        ->where('course_id', $courseId)
        ->whereHas('semesterCourseRegistration', function ($query) {
            $query->where('status', 'approved');
        })
        ->get();
        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(['Matric Number', 'Student Name', 'Course Name', 'Course Code','Assessment Score', 'Exam Score']);
        foreach ($exportaccess as $exportaccess) {

            $name = $exportaccess->student->user->first_name . ' ' . $exportaccess->student->user->last_name . ' ' . $exportaccess->student->user->other_name;
            $csv->insertOne([
                $exportaccess->student->matric_number,
                $name,
                $exportaccess->course->title,
                $exportaccess->course->code,
                $exportaccess->studentScore->assessment_score ?? '',
                $exportaccess->studentScore->exam_score ?? ''
            ]);
        }
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="assessments.csv"',
        ];
        return response($csv->getContent(), 200, $headers);
    }

    public function ImportAssessmentCsv(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'assessment_import' => 'required|mimes:csv,txt'
        ]);

        $teacher = Teacher::where('user_id',$this->authService->user()->id)->first();
        $assignment = TeacherAssignment::where('teacher_id',$teacher->id)
        ->where('course_id', $request->course_id)
        ->first();
        $csv = Reader::createFromPath($request->file('assessment_import')->getPathname());
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        DB::beginTransaction();
        try {
            foreach ($records as $record) {
                if (empty($record['Assessment Score']) || empty($record['Exam Score'])) {
                    // Skip this record or throw an error if you want to stop the import
                    return redirect()->back()->with('error', 'Assessment Score or Exam Score cannot be empty for Matric Number: ' . $record['Matric Number']);
                }

                $student = Student::where('matric_number', $record['Matric Number'])->firstOrFail();

                $totalScore = $record['Assessment Score'] + $record['Exam Score'];
                $grade = GradeSystem::getGrade($totalScore);
                $isFailed = $grade === 'F';
                $gradePoint = $this->calculateGradePoint($grade);
                // dd($gradePoint);

                StudentScore::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_id' => $assignment->course_id,
                        'academic_session_id' => $assignment->academic_session_id,
                        'semester_id' => $assignment->semester_id,
                    ],
                    [
                        'teacher_id' => $assignment->teacher_id,
                        'department_id' => $student->department_id,
                        'assessment_score' => $record['Assessment Score'],
                        'exam_score' => $record['Exam Score'],
                        'total_score' => $totalScore,
                        'grade' => $grade,
                        'is_failed' => $isFailed,
                        'grade_point'=>(float) $gradePoint
                    ]
                );
                 // cgpa

        $this->updateGpaRecord($student->id, $assignment->academic_session_id, $assignment->semester_id);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Scores imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while importing scores: ' . $e->getMessage());
        }
    }



    public function uploadresult(Request $uploadresult,$courseid){
        $validator = Validator::make($uploadresult->all(), [
            'scores.*.assessment' => 'required|numeric|min:0|max:40|integer',  // Changed to integer validation
        'scores.*.exam' => 'required|numeric|min:0|max:60|integer',      
        ], [
            'scores.*.assessment.required' => 'Assessment score is required for all students.',
            'scores.*.exam.required' => 'Exam score is required for all students.',
            'scores.*.assessment.max' => 'Assessment score cannot exceed 40.',
            'scores.*.exam.max' => 'Exam score cannot exceed 60.',
        ]);




        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $teacher = Teacher::where('user_id',$this->authService->user()->id)->first();
        $assignment = TeacherAssignment::where('teacher_id',$teacher->id)
        ->where('course_id', $courseid)
        ->first();
        try {
            DB::beginTransaction();

            foreach ($uploadresult->scores as $enrollmentId => $scoreData) {

                $enrollment = CourseEnrollment::findOrFail($enrollmentId);

                $totalScore = $scoreData['assessment'] + $scoreData['exam'];
                $grade = GradeSystem::getGrade($totalScore);
                $isFailed = $grade === 'F';
                $gradePoint = $this->calculateGradePoint($grade);

                if (!is_numeric($gradePoint)) {
                    throw new \Exception("Invalid grade point calculated: $gradePoint");
                }


                StudentScore::updateOrCreate(
                    [
                        'student_id' => $enrollment->student_id,
                        'course_id' => $assignment->course_id,
                        'academic_session_id' => $assignment->academic_session_id,
                        'semester_id' => $assignment->semester_id,
                    ],
                    [
                        'teacher_id' => $assignment->teacher_id,
                        'department_id' => $enrollment->student->department_id,
                      'assessment_score' => (int) $scoreData['assessment'],  // Force integer
        'exam_score' => (int) $scoreData['exam'],               // Force integer
        'total_score' => (int) $totalScore,  
                        'grade' => $grade,
                        'is_failed' => $isFailed,
                        'grade_point'=> (float) $gradePoint
                    ]
                );

                 // cgpa

        $this->updateGpaRecord($enrollment->student_id, $assignment->academic_session_id, $assignment->semester_id);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Scores have been saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving scores: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while saving scores. Please try again.');
        }
    }

    private function calculateGradePoint($grade)
    {
        switch ($grade) {
            case 'A': return 5.0;
            case 'B': return 4.0;
            case 'C': return 3.0;
            case 'D': return 2.0;
            default: return 0.0;
        }
    }

    private function updateGpaRecord($studentId ,$acamdicsession, $semesterId)
    {
        $enrollments = CourseEnrollment::whereHas('semesterCourseRegistration', function ($query) use ($semesterId) {
            $query->where('semester_id', $semesterId);
        })
            ->where('student_id', $studentId)
            ->with(['studentScore', 'course'])
            ->get();

        $totalGradePoints = 0;
        $totalCreditUnits = 0;

        foreach ($enrollments as $enrollment) {
            if ($enrollment->studentScore) {
                $totalGradePoints += $enrollment->studentScore->grade_point * $enrollment->course->credit_hours;
                $totalCreditUnits += $enrollment->course->credit_hours;
            }
        }

        $gpa = $totalCreditUnits > 0 ? $totalGradePoints / $totalCreditUnits : 0;

        $gpaRecord = GpaRecord::updateOrCreate(
            ['student_id' => $studentId, 'semester_id' => $semesterId, 'academic_session_id'=>$acamdicsession],
            ['gpa' => $gpa]
        );

        $this->updateCGPA($studentId);

        return $gpaRecord;
    }

    private function updateCGPA($studentId)
    {

        $gpaRecords = GpaRecord::where('student_id', $studentId)->get();
        $totalGPA = $gpaRecords->sum('gpa');
        $cgpa = $gpaRecords->count() > 0 ? $totalGPA / $gpaRecords->count() : 0;
        $updatestudent = Student::where('id',$studentId)->update([
            'cgpa'=>$cgpa
        ]);
        return $updatestudent;
        // foreach ($gpaRecords as $record) {
        //     $record->update(['cgpa' => $cgpa]);
        // }
    }
}
