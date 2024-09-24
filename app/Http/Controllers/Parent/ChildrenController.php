<?php

namespace App\Http\Controllers\Parent;

use App\Models\User;
use App\Models\Parents;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentScore;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Models\StudentsParent;
use App\Models\CourseEnrollment;
use App\Http\Controllers\Controller;

class ChildrenController extends Controller
{
    protected $authService;

    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService){

        $this->authService = $authService;
    }
    public function index(){
        $parent = Parents::where('user_id',$this->authService->user()->id)->first();
        $childrens = StudentsParent::with(['parent','student.user'])->where('parent_id',$parent->id)->get();
        return view('parent.childrens.index',[
            'childrens'=>$childrens
        ]);
    }

    public function view($id){
        $student = Student::with('user')->where('id',$id)->first();

        $profile = Student::where('id',$id)->first();
        $getuser = User::where('id',$profile->user_id)->first();
        // get results
        $studentId = $student->id;

        $availableResults = StudentScore::where('student_id', $studentId)
            ->select('academic_session_id', 'semester_id','teacher_id','student_id')
            ->with(['academicSession', 'semester', 'course'])
            ->groupBy('academic_session_id', 'semester_id','teacher_id','student_id')
            ->get()
            ->map(function ($result) {
                // $cgpa = StudentScore::where('student_id', $result->student_id)
                //     ->where('academic_session_id', $result->academic_session_id)
                //     ->where('semester_id', $result->semester_id)
                //     ->avg('total_score') / 20; // Assuming CGPA is on a 5-point scale

                return [
                    'session' => $result->academicSession->name,
                    // 'level' => $result->course->level,
                    'semester' => $result->semester->name,
                    'teacher'  => $result->teacher_id,
                    'sessionid'=>$result->academicSession->id,
                    'semesterid'=>$result->semester->id,
                    'studentid' =>$result->student_id
                    // 'cgpa' => number_format($cgpa, 2),
                ];
            });

            // get the teachers assigned to the student
            $teachersassigned = CourseEnrollment::with(['teacherAssigned.teacher.user','course'])
            ->where('student_id',$studentId)
            // ->where('status','approved')
            ->get();

            // get school fees
            $payments = Payment::with(['student.user','academicSession','semester','paymentType','paymentMethod','receipt'])->where('student_id',$id)->get();
          
        return view('parent.childrens.view',[
            'student'=>$student,
            'getuser'=>$getuser,
            'availableResults'=>$availableResults,
            'teachersassigned'=>$teachersassigned,
            'payments'=>$payments
        ]);
    }
    public function result($sessionid,$semesterid,$teacherid,$studentId){
        $profile = Student::where('id',$studentId)->first();
        $studentId = $profile->id;
        $studentresults = StudentScore::with(['student.user','course','academicSession','semester'])
        ->where('student_id',$studentId)
        ->where('academic_session_id',$sessionid)
        ->where('semester_id',$semesterid)
        ->where('teacher_id',$teacherid)
        ->where('status','approved')
        ->get();
        return view('parent.childrens.result',[
            'studentresults'=>$studentresults
        ]);
    }
}
