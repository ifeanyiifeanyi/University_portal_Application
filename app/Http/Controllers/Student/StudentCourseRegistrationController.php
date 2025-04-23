<?php

namespace App\Http\Controllers\Student;

use App\Models\Course;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Semester;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Models\AcademicSession;
use App\Models\CourseAssignment;
use App\Models\CourseEnrollment;
use App\Models\Courseregistration;
use App\Http\Controllers\Controller;
use App\Models\SemesterRegistration;
use App\Models\SemesterCourseRegistration;
use App\Http\Requests\ProceedSessionRequest;
use App\Http\Requests\CourseRegistrationRequest;
use App\Models\StudentFailedCourse;
use Illuminate\Support\Facades\Log;
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
            'reghistorys'=>$reghistorys,
            'student'=>$student
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

          $currentDepartment = Department::find($student->department_id);
          $levels = $currentDepartment ? $currentDepartment->levels : [];

          $checkdepartmentcourses = CourseAssignment::with(['course'])->where('department_id',$student->department_id)->get();
          
        return view('student.course.sessioncourse',[
            'semesters'=>$semesters,
            'academicsessions'=>$academicsessions,
            'student'=>$student,
            'checkdepartmentcourses'=>$checkdepartmentcourses,
            'levels'=>$levels,
            'currentDepartment'=>$currentDepartment
        ]);
    }

    public function proceedsession(ProceedSessionRequest $proceedsession){
        $student = Student::where('user_id',$this->authService->user()->id)->first();
         // check for existing session
        $checksession = SemesterCourseRegistration::where('student_id',$student->id)->where('semester_id',$proceedsession->semester)->where('academic_session_id',$proceedsession->session)->first();
        if($checksession){
            // return $checksession->id;
            return redirect(route('student.view.registercourse',['semester_regid'=>$checksession->id,'session_id'=>$checksession->semester_id,'semester_id'=>$checksession->academic_session_id,'level'=>$checksession->level]));
            // return redirect(route('student.view.sessioncourse'))->with('error','You have already registered courses for this session');
        }
    //     $checkpayment = Payment::where('student_id',$student->id)->where('academic_session_id',$proceedsession->session)->where('semester_id',$proceedsession->semester)->where('level',$proceedsession->level)->where('status','paid')->first();
    //     if (!$checkpayment) {
    //        return redirect()->route('student.view.sessioncourse')->with('error', 'You have not paid for the school fees for this session and semester');
    //    }

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
            'total_credit_hours'=>$maxCreditHours,
            'level'=>$proceedsession->level
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
        $selectcoursesassigned = CourseAssignment::with(['course'])->where('department_id',$student->department_id)->where('semester_id',$semester)->where('level',$level)->get();

        // get all the courses for that actual section
        $getsemestercourses = CourseAssignment::with(['course'])->where('semester_id',$semester)->get();

         // Retrieve failed courses for the student that haven't been retaken
    $failedCourses = StudentFailedCourse::with(['course'])
    ->where('student_id', $student->id)
    ->where('is_retaken', false)
    ->get();

     // Get already registered courses for this semester registration
     $registeredCourses = CourseEnrollment::where('student_id', $student->id)
     ->where('semester_course_registration_id', $semesterregid)
     ->pluck('course_id')
     ->toArray();

        return view('student.course.registercourse',[
            'courses'=>$selectcoursesassigned,
            'failedCourses' => $failedCourses,
            'semester'=>$semester,
            'session'=>$session,
            'semesterregid'=>$semesterregid,
            'semestercourses'=>$getsemestercourses,
            'semesterregistration'=>$semesterregistration,
            'level'=>$level,
            'registeredCourses' => $registeredCourses
        ]);
    }

    public function courseregister(CourseRegistrationRequest $courseregister) {
        $student = Student::where('user_id', $this->authService->user()->id)->first();
        
        // Check credit load limit
        $maxCreditHours = $student->department->semesters()
            ->where('semester_id', $courseregister->semester)
            ->firstOrFail()
            ->pivot
            ->max_credit_hours;
        
        if ($courseregister->TotalCreditLoadCount > $maxCreditHours) {
            return redirect()->back()->with('error', 'The credit load has exceeded the maximum allowed');
        }
    
        // Process regular courses
        if ($courseregister->has('course_id')) {
            foreach ($courseregister->course_id as $courseId) {
                CourseEnrollment::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_id' => $courseId,
                        'semester_course_registration_id' => $courseregister->semesterregid
                    ],
                    [
                        'department_id' => $student->department_id,
                        'level' => $courseregister->level,
                        'academic_session_id' => $courseregister->session,
                        'is_carryover' => false
                    ]
                );
            }
        }
    
        // Process carry-over courses
        if ($courseregister->has('carry_over_course_id')) {
            foreach ($courseregister->carry_over_course_id as $carryOverCourseId) {
                $failedCourse = StudentFailedCourse::where('student_id', $student->id)
                    ->where('course_id', $carryOverCourseId)
                    ->where('is_retaken', false)
                    ->first();
    
                if ($failedCourse) {
                    CourseEnrollment::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'course_id' => $carryOverCourseId,
                            'semester_course_registration_id' => $courseregister->semesterregid
                        ],
                        [
                            'department_id' => $student->department_id,
                            'level' => $courseregister->level,
                            'academic_session_id' => $courseregister->session,
                            'is_carryover' => true
                        ]
                    );
    
                    $failedCourse->is_retaken = true;
                    $failedCourse->save();
                }
            }
        }
    
        return redirect(route('student.view.courseregistration'))->with('success', 'Courses registered successfully and awaiting approval');
    }
    // public function courseregister(CourseRegistrationRequest $courseregister){
    // //    check if it exceeds the total credit load

    //     $student = Student::where('user_id',$this->authService->user()->id)->first();
    //     $maxCreditHours = $student->department->semesters()
    //         ->where('semester_id', $courseregister->semester)
    //         ->firstOrFail()
    //         ->pivot
    //         ->max_credit_hours;
    //         if($courseregister->TotalCreditLoadCount > $maxCreditHours){
    //             return redirect()->back()->with('error','The credit load have exceeded the initial credit load');
    //         }
    //     foreach ($courseregister->course_id as $courseId) {
    //         CourseEnrollment::create([
    //             'student_id'=>$student->id,
    //             'course_id'=> $courseId,
    //             'department_id'=>$student->department_id,
    //             'level'=>$courseregister->level,
    //             'semester_course_registration_id'=>$courseregister->semesterregid,
    //             'academic_session_id'=>$courseregister->session
    //         ]);
    //     }

    //      // Register carry-over courses
    // if ($courseregister->has('carry_over_course_id')) {
    //     foreach ($courseregister->carry_over_course_id as $carryOverCourseId) {
    //         // Find the failed course record
    //         $failedCourse = StudentFailedCourse::where('student_id', $student->id)
    //             ->where('course_id', $carryOverCourseId)
    //             ->where('is_retaken', false)
    //             ->first();

    //         if ($failedCourse) {
    //             // Create course enrollment for carry-over course
    //             CourseEnrollment::create([
    //                 'student_id' => $student->id,
    //                 'course_id' => $carryOverCourseId,
    //                 'department_id' => $student->department_id,
    //                 'level' => $courseregister->level,
    //                 'semester_course_registration_id' => $courseregister->semesterregid,
    //                 'academic_session_id' => $courseregister->session,
    //                 'is_carryover' => true, // Add this field to your course_enrollments table
    //             ]);

    //             // Mark the failed course as retaken
    //             $failedCourse->is_retaken = true;
    //             $failedCourse->save();
    //         }
    //     }
    // }
    //     return redirect(route('student.view.courseregistration'))->with('success','Courses created successfully and awaiting approval');
    // }

public function checkCreditLoad(Request $request)
{
    $totalCreditLoad = $request->totalCreditLoad;

    // return $totalCreditLoad;
    $student = Student::where('user_id', $this->authService->user()->id)->first();
    $maxCreditLoad = $student->department->semesters()
        ->where('semester_id', $request->semester)
        ->firstOrFail()
        ->pivot
        ->max_credit_hours;

    return response()->json([
        'totalCreditLoad' => $totalCreditLoad,
        'maxCreditLoad'=> $maxCreditLoad,
        'exceedsLimit' => $totalCreditLoad > $maxCreditLoad
    ]);
}

    public function levels(Department $department)
    {
        return response()->json($department->levels);
    }
}
