<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use League\Csv\Writer;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\Semester;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Models\CourseAssignment;
use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use Illuminate\Support\Facades\Response;
use Symfony\Component\ErrorHandler\Debug;

class DepartmentController extends Controller
{
    public function index()
    {
        $faculties = Faculty::query()->latest()->get();
        $departments = Department::query()->oldest()->get();
        $programs = Program::all();
        $users = User::where('user_type', 2)->get();
        return view('admin.departments.index', compact('faculties', 'departments', 'programs', 'users'));
    }



    public function store(DepartmentRequest $request)
    {
        try {
            $validatedData = $request->validated();
            // Available alpha characters
            $characters = 'CONSO';

            // generate a pin based on 2 * 7 digits + a random character
            $validatedData['code'] = mt_rand(100, 999) . $characters;
            Department::create($validatedData);
            $notification = [
                'status' => 'success',
                'message' => 'Department created successfully'
            ];

            return redirect()->back()->with($notification);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create department',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function edit($id)
    {
        $departmentSingle = Department::findOrFail($id);
        $faculties = Faculty::query()->latest()->get();
        $departments = Department::query()->latest()->get();
        $programs = Program::all();
        $users = User::where('user_type', 2)->get();
        return view('admin.departments.create', compact('departmentSingle', 'faculties', 'departments', 'programs', 'users'));
    }
    public function update(DepartmentRequest $request, Department $department)
    {
        $department->update($request->validated());
        // Log the department update
        ActivityLogHelper::logDepartmentActivity('Updated', $department);
        $notification = [
            'message' => 'Department Updated Successfully!!',
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.department.view')->with($notification);
    }


    public function show($id, Request $request)
    {
        $department = Department::findOrFail($id);
        $query = CourseAssignment::with(['course', 'semester.academicSession', 'teacherAssignment.teacher.user'])->where('department_id', $id);



        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('course', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })->orWhereHas('teacherAssignments.teacher.user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('session')) {
            $query->whereHas('semester.academicSession', function ($q) use ($request) {
                $q->where('name', $request->input('session'));
            });
        }

        if ($request->has('semester')) {
            $query->whereHas('semester', function ($q) use ($request) {
                $q->where('name', $request->input('semester'));
            });
        }

        if ($request->has('level')) {
            $query->where('level', $request->input('level'));
        }

        $assignments = $query->orderBy('semester_id', 'desc')->paginate(15);


        return view('admin.departments.detail', compact('department', 'assignments'));
    }


    public function destroy(Department $department)
    {

        // Log the department deletion
        ActivityLogHelper::logDepartmentActivity('Deleted', $department);
        $department->delete();
        $notification = [
            'message' => 'Department Deleted Successfully!!',
            'alert-type' => 'success'
        ];
        return redirect()->back()->with($notification);
    }

    // fetch the department academic levels
    public function levels(Department $department)
    {
        return response()->json($department->levels);
    }



    public function teacherCourses($id, Request $request)
    {
        $department = Department::findOrFail($id);
        $query = CourseAssignment::with(['course', 'semester.academicSession', 'teacherAssignment.teacher.user'])
            ->where('department_id', $id);

        // Filtering
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('course', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })->orWhereHas('teacherAssignments.teacher.user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('session')) {
            $query->whereHas('semester.academicSession', function ($q) use ($request) {
                $q->where('name', $request->input('session'));
            });
        }

        if ($request->has('semester')) {
            $query->whereHas('semester', function ($q) use ($request) {
                $q->where('name', $request->input('semester'));
            });
        }

        if ($request->has('level')) {
            $query->where('level', $request->input('level'));
        }

        $assignments = $query->orderBy('semester_id', 'desc')->paginate(15);

        $academicSessions = AcademicSession::all();
        $semesters = Semester::all();


        return view('admin.departments.teacher_course', compact(
            'department',
            'assignments',
            'academicSessions',
            'semesters',
        ));
    }

    public function departmentStudent($id)
    {
        $departmentStudent = Department::with(['students.user'])->findOrFail($id);
        return view('admin.departments.students', compact('departmentStudent'));
    }



    public function exportCsv($id)
    {
        $department = Department::findOrFail($id);
        $assignments = CourseAssignment::with(['course', 'semester.academicSession', 'teacherAssignment.teacher.user'])
            ->where('department_id', $id)
            ->orderBy('semester_id', 'desc')
            ->get();

        $csv = Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(['Course', 'Teacher', 'Semester', 'Academic Session', 'Level']);

        foreach ($assignments as $assignment) {
            $teacherName = '';
            if ($assignment->teacherAssignments->isNotEmpty()) {
                $teacherNames = [];
                foreach ($assignment->teacherAssignments as $teacherAssignment) {
                    if ($teacherAssignment->teacher && $teacherAssignment->teacher->user) {
                        $teacherNames[] = $teacherAssignment->teacher->title_full_name();
                    }
                }
                $teacherName = implode(', ', $teacherNames);
            }

            $csv->insertOne([
                $assignment->course->title,
                $teacherName,
                $assignment->semester->name,
                $assignment->semester->academicSession->name,
                $assignment->level,
            ]);
        }

        $filename = 'department_' . $department->name . '_courses.csv';
        $csvContent = $csv->output();

        // Log the department deletion
        ActivityLogHelper::logDepartmentActivity('Export Teacher, in department', $department);
        return response()->streamDownload(
            function () use ($csvContent) {
                echo $csvContent;
            },
            $filename
        );
    }





    //export students for the department in excel
    public function exportStudentsForDepartment($id)
    {
        $department = Department::findOrFail($id);
        $assignments = CourseAssignment::with(['course', 'semester.academicSession', 'teacherAssignment.teacher.user'])
            ->where('department_id', $id)
            ->orderBy('semester_id', 'desc')
            ->get();

        $csv = Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(['Mat No.', 'Student', 'Gender', 'Email', 'Phone', 'Level']);

        foreach ($department->students as $student) {
            $csv->insertOne([
                $student->matric_number,
                $student->user->fullName(),
                $student->gender,
                $student->user->email,
                $student->user->phone,
                $student->current_level,
            ]);
        }

        // Log the department deletion
        ActivityLogHelper::logDepartmentActivity('EXport Students, in department', $department);

        $filename = 'department_' . $department->name . '_students.csv';
        $csvContent = $csv->output();

        return response()->streamDownload(
            function () use ($csvContent) {
                echo $csvContent;
            },
            $filename
        );
    }
}
