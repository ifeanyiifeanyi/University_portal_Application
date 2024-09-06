<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use App\Models\Student;
use App\Models\StudentScore;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Controllers\Controller;

class StudentResultController extends Controller
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
        $profile = Student::where('user_id',$this->authService->user()->id)->first();
        $studentId = $profile->id;

        $availableResults = StudentScore::where('student_id', $studentId)
            ->select('academic_session_id', 'semester_id','teacher_id')
            ->with(['academicSession', 'semester', 'course'])
            ->groupBy('academic_session_id', 'semester_id','teacher_id')
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
                    'semesterid'=>$result->semester->id
                    // 'cgpa' => number_format($cgpa, 2),
                ];
            });
            // dd($availableResults);
        return view('student.result.index',[
            'availableResults'=>$availableResults
        ]);
    }
    // public function getAvailableResults(Request $request)
    // {
    //     $studentId = $request->student_id;

    //     $availableResults = StudentScore::where('student_id', $studentId)
    //         ->select('academic_session_id', 'semester_id')
    //         ->with(['academicSession', 'semester', 'course'])
    //         ->groupBy('academic_session_id', 'semester_id')
    //         ->get()
    //         ->map(function ($result) {
    //             $cgpa = StudentScore::where('student_id', $result->student_id)
    //                 ->where('academic_session_id', $result->academic_session_id)
    //                 ->where('semester_id', $result->semester_id)
    //                 ->avg('total_score') / 20; // Assuming CGPA is on a 5-point scale

    //             return [
    //                 'session' => $result->academicSession->name,
    //                 'level' => $result->course->level,
    //                 'semester' => $result->semester->name,
    //                 'cgpa' => number_format($cgpa, 2),
    //             ];
    //         });

    //     return response()->json($availableResults);
    // }
    public function view($sessionid,$semesterid,$teacherid){
        $profile = Student::where('user_id',$this->authService->user()->id)->first();
        $studentId = $profile->id;
        $studentresults = StudentScore::with(['student.user','course','academicSession','semester'])
        ->where('student_id',$studentId)
        ->where('academic_session_id',$sessionid)
        ->where('semester_id',$semesterid)
        ->where('teacher_id',$teacherid)
        ->where('status','approved')
        ->get();
        // dd($studentresults);
        return view('student.result.view',[
            'studentresults'=>$studentresults
        ]);
    }
}
