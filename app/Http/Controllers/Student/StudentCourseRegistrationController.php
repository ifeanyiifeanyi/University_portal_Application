<?php

namespace App\Http\Controllers\Student;

use App\Models\Course;
use App\Models\Student;
use App\Models\Semester;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Models\AcademicSession;
use App\Models\CourseAssignment;
use App\Models\Courseregistration;
use App\Http\Controllers\Controller;
use App\Models\SemesterRegistration;
use App\Http\Requests\ProceedSessionRequest;
use App\Http\Requests\CourseRegistrationRequest;

class StudentCourseRegistrationController extends Controller
{
    protected $authService;

    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService){

        $this->authService = $authService;
    }
    public function courseregistration(){
    //   get the student id of the user
    $student = Student::where('user_id',$this->authService->user()->id)->first();
    // query the session_registration table to get the history
    $reghistorys = SemesterRegistration::with(['semester','AcademicSession'])->where('student_id',$student->id)->get();
        return view('student.course.registration',[
            'reghistorys'=>$reghistorys
        ]);
    }

    public function viewregistered($id){
        $registered = Courseregistration::with(['course'])->where('semester_regid',$id)->get();
        return view('student.course.courseregistered',[
            'registered'=>$registered
        ]);
    }

    public function sessioncourse(){
          // load the semester
          $semesters = Semester::get();
          // load the academic sessions
          $academicsessions = AcademicSession::all();
          // load the studentprofile
          $student = Student::where('user_id',$this->authService->user()->id)->first();
          
        return view('student.course.sessioncourse',[
            'semesters'=>$semesters,
            'academicsessions'=>$academicsessions,
            'student'=>$student
        ]);
    }

    public function proceedsession(ProceedSessionRequest $proceedsession){
         // check for existing session
        $checksession = SemesterRegistration::where('semester_id',$proceedsession->semester)->where('academic_session_id',$proceedsession->session)->exists();
        if($checksession){
            return redirect(route('student.view.sessioncourse'))->with('success','You have already registered courses for this session');
        }
        $createsemesterreggistration = SemesterRegistration::create([
            'semester_id'=>$proceedsession->semester,
            'academic_session_id'=>$proceedsession->session,
            'level'=>$proceedsession->level,
            'student_id'=>$proceedsession->student_id,
            'user_id'=>$this->authService->user()->id
        ]);
        if($createsemesterreggistration){
            // redirect user to the main course registration page
            return redirect(route('student.view.registercourse',['semester_regid'=>$createsemesterreggistration->id,'session_id'=>$createsemesterreggistration->semester_id,'semester_id'=>$createsemesterreggistration->academic_session_id,'level'=>$createsemesterreggistration->level]));
        }
    }
    public function registercourse($semesterregid,$session,$semester,$level){
        // load the studentprofile
        $student = Student::where('user_id',$this->authService->user()->id)->first();
        $selectcoursesassigned = CourseAssignment::with(['course'])->where('department_id',$student->department_id)->where('semester_id',$semester)->where('level',$level)->get();

        // get all the courses for that actual section
        $getsemestercourses = CourseAssignment::with(['course'])->where('semester_id',$semester)->get();
        return view('student.course.registercourse',[
            'courses'=>$selectcoursesassigned,
            'semester'=>$semester,
            'session'=>$session,
            'level'=>$level,
            'semesterregid'=>$semesterregid,
            'semestercourses'=>$getsemestercourses
        ]);
    }
    public function courseregister(CourseRegistrationRequest $courseregister){
       
        $student = Student::where('user_id',$this->authService->user()->id)->first();
        // $totalCreditLoad = 0;
        // $totalCreditLoad += $course->credit_hours;

        foreach ($courseregister->course_id as $courseId) {
            Courseregistration::create([
                'course_id'=> $courseId, 
                'department_id'=>$student->department_id, 
                'semester_id'=>$courseregister->semester, 
                'session_id'=>$courseregister->session,
                'user_id'=>$this->authService->user()->id,
                'student_id'=>$student->id,
                'level'=>$courseregister->level,
                'semester_regid'=>$courseregister->semesterregid
            ]);
        }
        return redirect(route('student.view.courseregistration'))->with('success','Courses created successfully and awaiting approval');
    }

    public function checkCreditLoad(Request $request)
{
    $validatedData = $request->validate([
        'course_id' => 'array|required',
        'course_id.*' => 'exists:courses,id'
    ]);

    $totalCreditLoad = 0;

    foreach ($validatedData['course_id'] as $courseId) {
        $course = Course::find($courseId);
        $totalCreditLoad += $course->credit_hours;
    }

    // Define the maximum allowed credit load
    $maxCreditLoad = 3;

    return response()->json([
        'totalCreditLoad' => $totalCreditLoad,
        'exceedsLimit' => $totalCreditLoad > $maxCreditLoad
    ]);
}

    public function levels(Department $department)
    {
        return response()->json($department->levels);
    }
}
