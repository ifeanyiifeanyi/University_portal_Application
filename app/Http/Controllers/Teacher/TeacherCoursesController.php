<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Models\CourseEnrollment;
use App\Models\TeacherAssignment;
use App\Http\Controllers\Controller;

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

    public function students($courseId){
        // get all the students that registered for the course
        // $studentsregistered = CourseEnrollment::with(['course','semesterCourseRegistration'])->where('course_id',$id)->get();

          // Get students who registered for the given course and whose registration has been approved
          $students = CourseEnrollment::with(['student.user','course','department'])  // Eager load the student relationship
          ->where('course_id', $courseId)  // Filter by course ID
          ->whereHas('semesterCourseRegistration', function ($query) {
              // Join with SemesterCourseRegistration and filter by approved status
              $query->where('status', 'approved');
          })
          ->get();  // Get only the student information
          if(!$students){
            abort(403,'Nothing found');
          }
          return view('teacher.courses.enrolledstudents',[
            'students'=>$students,
            'courseId'=>$courseId
        ]);
    }

    public function calculateGrade(Request $request)
    {
        $total = $request->input('total');
        $grade = '';

        if ($total >= 0 && $total <= 49) {
            $grade = 'D';
        } elseif ($total >= 50 && $total <= 60) {
            $grade = 'C';
        } elseif ($total >= 61 && $total <= 69) {
            $grade = 'B';
        } elseif ($total >= 70 && $total <= 100) {
            $grade = 'A';
        } else {
            $grade = 'Invalid';
        }

        return response()->json(['grade' => $grade]);
    }

    public function exportassessment($courseId){
        $exportaccess = CourseEnrollment::with(['student.user','course','department'])  // Eager load the student relationship
          ->where('course_id', $courseId)  // Filter by course ID
          ->whereHas('semesterCourseRegistration', function ($query) {
              // Join with SemesterCourseRegistration and filter by approved status
              $query->where('status', 'approved');
          })
          ->get();  // Get only the student information

        //   dd($exportaccess);
        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(['Studentname', 'Student Department', 'Course name', 'Course code']);
        foreach ($exportaccess as $exportaccess) {
            
            $name = $exportaccess->student->user->first_name . ' ' . $exportaccess->student->user->last_name . ' ' . $exportaccess->student->user->other_name;
            $csv->insertOne([
                $name,
                $exportaccess->department->name,
                $exportaccess->course->title,
                $exportaccess->course->code,
            ]);
        }
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="course_registrations.csv"',
        ];
        return response($csv->getContent(), 200, $headers);
    }

    public function ImportAssessmentCsv(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'assessment_import' => 'required|mimes:csv,txt'
        ]);

        // Retrieve the uploaded file
        $assessmentfile = $request->file('assessment_import');

        // Create a Reader instance from the file
        $csv = \League\Csv\Reader::createFromPath($assessmentfile->getRealPath(), 'r');
        $csv->setHeaderOffset(0); // Set the header offset to treat the first row as headers

        // Iterate over the CSV records and insert them into the database
        foreach ($csv as $record) {
        
        }

        return redirect()->back()->with('success', 'Assessment imported successfully.');
    }
}
