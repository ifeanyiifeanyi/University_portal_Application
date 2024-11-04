<?php

namespace App\Http\Controllers\Teacher;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Semester;
use App\Models\StudentScore;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Models\AcademicSession;
use App\Models\CourseEnrollment;
use App\Services\TeacherService;
use App\Models\TeacherAssignment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Profilerequest;
use WisdomDiala\Countrypkg\Models\Country;

class TeacherController extends Controller
{
    protected $authService;

    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService)
    {

        $this->authService = $authService;
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login.view');
        }

        $teacher = Teacher::where('user_id', $this->authService->user()->id)->first();
        $currentSemesterId = Semester::where('is_current', true)->first()->id;
        $currentSessionId = AcademicSession::where('is_current', true)->first()->id;
        // dd($currentSemesterId, $currentSessionId);

        // Get counts for dashboard
        $coursesassignedcount = TeacherAssignment::where('teacher_id', $teacher->id)
            ->where('academic_session_id', $currentSessionId)  // Add these variables
            ->where('semester_id', $currentSemesterId)        // based on your system
            ->count();
        // dd($coursesassignedcount);


        // Get total students across all courses
        $totalStudents = CourseEnrollment::whereHas('course.teacherAssignments', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->count();

        // Get pending tasks (this is an example - adjust based on your needs)
        $pendingTasks = StudentScore::where('teacher_id', $teacher->id)
            ->whereNull('total_score')
            ->count();

        return view('teacher.dashboard', [
            'teacher' => $teacher,
            'coursesassignedcount' => $coursesassignedcount,
            'totalStudents' => $totalStudents,
            'pendingTasks' => $pendingTasks
        ]);
    }

    public function profile()
    {
        $countries = Country::all();
        $getuser = User::where('id', $this->authService->user()->id)->first();
        $profile = Teacher::where('user_id', $this->authService->user()->id)->first();
        return view('teacher.profile.profile', [
            'profile' => $profile,
            'getuser' => $getuser,
            'countries' => $countries,
        ]);
    }

    public function createprofile(Profilerequest $createprofile, TeacherService $teacherservice)
    {
        return $teacherservice->createprofile($createprofile);
    }
    public function updateprofile(Profilerequest $updateprofile, TeacherService $teacherservice)
    {
        return $teacherservice->updateprofile($updateprofile);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login.view');
    }
}
