<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CourseAssignment;
use App\Services\AuthService;
use App\Models\Student;
use App\Models\Course;
use App\Models\StudyMaterial;

class StudentStudyMaterialsController extends Controller
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
        $student = Student::where('user_id',$this->authService->user()->id)->first();

        $checkdepartmentcourses = CourseAssignment::with(['course'])->where('department_id',$student->department_id)
        ->where('level',$student->department->getLevelNumber($student->current_level))
        ->get();
// dd($checkdepartmentcourses);
        return view('student.studymaterials.index',[
            'courses'=>$checkdepartmentcourses
        ]);
    }

    public function view($id){
        $course = Course::findOrFail($id);
        $studymaterials = StudyMaterial::with(['course'])->where('course_id',$course->id)->where('status','approved')->get();
        return view('student.studymaterials.view',[
            'studentmaterials'=>$studymaterials,
        ]);
    }
    public function viewmaterial($id){
        $studyMaterial = StudyMaterial::with(['course'])->where('id',$id)->where('status','approved')->firstOrFail();

        // if ($studyMaterial->file_type === 'pdf') {
        //     $fileUrl = $studyMaterial->file_path;
        // } else {
        //     $fileUrl = $studyMaterial->file_path;
        // }
        
        // return view('student.studymaterials.material', compact('studyMaterial'));
        dd($studyMaterial);
        
    }

    public function viewFile($fileName)
{
    // Define the path to your files
    $filePath = public_path('admin/lecturers/studymaterials/' . $fileName);

    // Check if the file exists
    if (!file_exists($filePath)) {
        abort(404, 'File not found.');
    }

    // Serve the file with Content-Disposition: inline
    return response()->file($filePath, [
        'Content-Disposition' => 'inline',
        'Content-Type' => mime_content_type($filePath), // Automatically detects the file type
    ]);
}
}
