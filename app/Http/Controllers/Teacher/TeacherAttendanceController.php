<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Teacher;
use App\Models\Semester;
use App\Models\Attendancee;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Models\AcademicSession;
use App\Models\CourseEnrollment;
use App\Models\TeacherAssignment;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;

class TeacherAttendanceController extends Controller
{
    protected $authService;

    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService){

        $this->authService = $authService;
    }

    public function attendance()
{
    $teacher = Teacher::where('user_id', $this->authService->user()->id)->first();
    
    $getattendances = Attendance::where('teacher_id', $teacher->id)
        ->with([
            'academicSession',
            'course',
            'department',
            'semester',
            'studentAttendances' => function($query) {
                $query->with('student.user');
            }
        ])
        ->orderBy('date', 'desc')
        ->get()
        ->map(function ($attendance) {
            return [
                'attendanceid' => $attendance->id,
                'session' => $attendance->academicSession->name,
                'sessionid' => $attendance->academicSession->id,
                'course' => $attendance->course->title,
                'department' => $attendance->department->name,
                'semester' => $attendance->semester->name,
                'semesterid' => $attendance->semester->id,
                'departmentid' => $attendance->department->id,
                'courseid' => $attendance->course->id,
                'date' => $attendance->date,
                'start_time' => $attendance->start_time,
                'end_time' => $attendance->end_time,
                'total_students' => $attendance->studentAttendances->count(),
                'present_students' => $attendance->studentAttendances->where('status', 'present')->count()
            ];
        });

    return view('teacher.attendance.index', [
        'attendances' => $getattendances
    ]);
}
    // public function attendance(){
    //     $teacher = Teacher::where('user_id',$this->authService->user()->id)->first();
    //     $getattendances = Attendance::where('teacher_id',$teacher->id)
    //     ->select('id','academic_session_id','teacher_id','course_id','department_id','semester_id','date')
    //     ->with(['academicSession', 'course','department','semester'])
    //     ->groupBy('id','academic_session_id','teacher_id','course_id','department_id','semester_id','date')
    //     ->get()
    //     ->map(function ($result) {
    //         return [
    //             'attendanceid'=>$result->id,
    //             'session' => $result->academicSession->name,
    //             'teacher'  => $result->teacher_id,
    //             'sessionid'=>$result->academicSession->id,
    //             'course'=>$result->course->title,
    //             'department'=>$result->department->name,
    //             'semester'=>$result->semester->name,
    //             'semesterid'=>$result->semester->id,
    //             'departmentid'=>$result->department->id,
    //             'courseid'=>$result->course->id,
    //             'date'=>$result->lecture_date
    //         ];
    //     });
    //     return view('teacher.attendance.index',[
    //         'attendances'=>$getattendances
    //     ]);
    // }
    public function view($attendanceid,$departmentid,$courseid){
        // view attendances
        $teacher = Teacher::where('user_id',$this->authService->user()->id)->first();
        $attendances = Attendancee::with(['academicSession', 'course','department','semester','student.user'])
        ->where('teacher_id',$teacher->id)
        ->where('department_id',$departmentid)
        ->where('course_id',$courseid)
        ->where('attendance_id',$attendanceid)
        ->get();
        return view('teacher.attendance.view',[
            'attendances'=>$attendances
        ]);
    }
    public function create(){
        $teacher = Teacher::where('user_id',$this->authService->user()->id)->first();
        // get all the departments assigned to the teacher
        $coursesassigned = TeacherAssignment::with(['course','department','semester','academicSession'])->where('teacher_id',$teacher->id)->get();
        return view('teacher.attendance.create',[
            'coursesassigned'=>$coursesassigned
        ]);
    }
    public function createattendance($sessionid,$semesterid,$departmentid,$courseid){
        $teacher = Teacher::where('user_id', $this->authService->user()->id)->first();

    // Eager load the studentScore with additional constraints
    $students = CourseEnrollment::with(['student.user', 'course', 'department'])
    ->where('course_id', $courseid)
    ->where('department_id',$departmentid)
    ->where('academic_session_id',$sessionid)
    ->whereHas('semesterCourseRegistration', function ($query) {
        $query->where('status', 'approved');
    })
    ->get();
        return view('teacher.attendance.createattendance',[
            'students'=>$students,
            'semesterid'=>$semesterid
        ]);
    }

    public function createstudentAttendance(Request $request) 
{
    $validated = $request->validate([
        'date' => 'required',
        'start_time' => 'required',
        'end_time' => 'required'
    ]);

    $teacher = Teacher::where('user_id', $this->authService->user()->id)->first();
    
    // Get attendance data array properly
    $attendanceData = $request->input('attendance');
    $firstAttendData = array_values($attendanceData)[0];
    
    // Create single attendance record
    $createattendance = Attendance::create([
        'semester_id' => $firstAttendData['semester_id'],
        'course_id' => $firstAttendData['course_id'],
        'academic_session_id' => $firstAttendData['session_id'],
        'department_id' => $firstAttendData['department_id'],
        'teacher_id' => $teacher->id,
        'date' => $request->date,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'notes' => $request->notes
    ]);

    // Create individual student attendance records
    foreach ($attendanceData as $attendData) {
        Attendancee::create([
            'student_id' => $attendData['student_id'],
            'course_id' => $attendData['course_id'],
            'department_id' => $attendData['department_id'],
            'teacher_id' => $teacher->id,
            'attendance_id' => $createattendance->id,
            'status' => isset($attendData['status']) ? 'present' : 'absent'
        ]);
    }

    return redirect(route('teacher.view.attendance'))
        ->with('success', 'Attendance records created successfully');
}

//     public function createstudentAttendance(Request $request)
// {
//     $validated = $request->validate([
//        'date'=>'required',
//        'start_time'=>'required',
//        'end_time'=>'required'
//     ]);
//     $teacher = Teacher::where('user_id',$this->authService->user()->id)->first();
//     foreach ($request->attendance as $attendData) {
    
//         $createattendance = Attendance::Create(
//             [
//                 'semester_id' => $attendData['semester_id'],
//                 'course_id' => $attendData['course_id'],
//                 'academic_session_id' => $attendData['session_id'],
//                 'department_id' => $attendData['department_id'],
//                 'teacher_id' => $teacher->id,
//                 'date'=>$request->date,
//                 'start_time'=>$request->start_time,
//                 'end_time'=>$request->end_time,
//                 'notes'=>$request->notes
//             ]
//     );
//         Attendancee::Create(
//             [
//                 'student_id' => $attendData['student_id'],
//                 'course_id' => $attendData['course_id'],
//                 'department_id' => $attendData['department_id'],
//                 'teacher_id' => $teacher->id,
//                 'attendance_id'=>$createattendance->id,
//                 'status' => isset($attendData['status']) ? 'present' : 'absent'
//             ]
//         );
//     }
//     return redirect(route('teacher.view.attendance'))->with('success', 'Attendance records created successfully');
// }

    public function updateAttendance(Request $request)
{
    $teacher = Teacher::where('user_id',$this->authService->user()->id)->first();
   
    foreach ($request->attendance as $attendData) {
       
        // $attendance = Attendancee::where('student_id', $attendData['student_id'])
        //     ->where('department_id', $attendData['department_id'])
        //     ->where('course_id', $attendData['course_id'])
        //     ->where('teacher_id',$teacher->id)
        //     ->where('attendance_id',$attendData['attendance_id'])
        //     ->get();

        // if ($attendance) {
        //     $attendance->status = isset($attendData['status']) ? 'present' : 'absent';
        //     $attendance->save();
        // }

        Attendancee::updateOrCreate(
            [
                'student_id' => $attendData['student_id'],
                'course_id' => $attendData['course_id'],
                'department_id' => $attendData['department_id'],
                'teacher_id' => $teacher->id,
                'attendance_id'=>$attendData['attendance_id']
            ],
            [
                'status' => isset($attendData['status']) ? 'present' : 'absent'
            ]
        );
    }
    return redirect()->back()->with('success', 'Attendance updated successfully!');
}
}
