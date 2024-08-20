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
use App\Models\CourseEnrollment;
use App\Models\SemesterCourseRegistration;

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
    $reghistorys = SemesterCourseRegistration::with(['semester','AcademicSession'])->where('student_id',$student->id)->get();
        return view('student.course.registration',[
            'reghistorys'=>$reghistorys
        ]);
    }

    public function viewregistered($id){
        $registered = CourseEnrollment::with(['course','semesterCourseRegistration'])->where('semester_course_registration_id',$id)->get();
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
        $checksession = SemesterCourseRegistration::where('semester_id',$proceedsession->semester)->where('academic_session_id',$proceedsession->session)->exists();
        if($checksession){
            return redirect(route('student.view.sessioncourse'))->with('error','You have already registered courses for this session');
        }
        $student = Student::findOrFail($proceedsession->student_id);
        $maxCreditHours = $student->department->semesters()
            ->where('semester_id', $proceedsession->semester)
            ->firstOrFail()
            ->pivot
            ->max_credit_hours;
            
        $createsemesterreggistration = SemesterCourseRegistration::create([
            'semester_id'=>$proceedsession->semester,
            'academic_session_id'=>$proceedsession->session,
            'student_id'=>$proceedsession->student_id,
            'total_credit_hours'=>$maxCreditHours
        ]);
        if($createsemesterreggistration){
            // redirect user to the main course registration page
            return redirect(route('student.view.registercourse',['semester_regid'=>$createsemesterreggistration->id,'session_id'=>$createsemesterreggistration->semester_id,'semester_id'=>$createsemesterreggistration->academic_session_id,'level'=>$proceedsession->level]));
        }
    }
    public function registercourse($semesterregid,$session,$semester,$level){
        // load the studentprofile
        $semesterregistration = SemesterCourseRegistration::where('id',$semesterregid)->first();
        $student = Student::where('user_id',$this->authService->user()->id)->first();
        $selectcoursesassigned = CourseAssignment::with(['course'])->where('department_id',$student->department_id)->where('semester_id',$semester)->get();

        // get all the courses for that actual section
        $getsemestercourses = CourseAssignment::with(['course'])->where('semester_id',$semester)->get();
        return view('student.course.registercourse',[
            'courses'=>$selectcoursesassigned,
            'semester'=>$semester,
            'session'=>$session,
            'semesterregid'=>$semesterregid,
            'semestercourses'=>$getsemestercourses,
            'semesterregistration'=>$semesterregistration,
            'level'=>$level
        ]);
    }
    public function courseregister(CourseRegistrationRequest $courseregister){
    //    check if it exceeds the total credit load

        $student = Student::where('user_id',$this->authService->user()->id)->first();
        $maxCreditHours = $student->department->semesters()
            ->where('semester_id', $courseregister->semester)
            ->firstOrFail()
            ->pivot
            ->max_credit_hours;
            if($courseregister->TotalCreditLoadCount > $maxCreditHours){
                return redirect()->back()->with('error','The credit load have exceeded the initial credit load');
            }
        foreach ($courseregister->course_id as $courseId) {
            CourseEnrollment::create([
                'student_id'=>$student->id,
                'course_id'=> $courseId,
                'department_id'=>$student->department_id,
                'level'=>$courseregister->level,
                'semester_course_registration_id'=>$courseregister->semesterregid,
                'academic_session_id'=>$courseregister->session,
                // 'course_id'=> $courseId, 
                // 'department_id'=>$student->department_id,

                // 'semester_id'=>$courseregister->semester, 
                // 'session_id'=>$courseregister->session,
                // 'user_id'=>$this->authService->user()->id,
                // 'student_id'=>$student->id,
                // 'level'=>$courseregister->level,
                // 'semester_regid'=>$courseregister->semesterregid
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
