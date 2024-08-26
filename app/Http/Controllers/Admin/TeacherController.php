<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Course;
use League\Csv\Reader;
use League\Csv\Writer;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Department;
use App\Models\GradeSystem;
use Illuminate\Support\Str;
use App\Models\StudentScore;
use Illuminate\Http\Request;
use App\Models\CourseEnrollment;
use App\Models\TeacherAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateTeacherRequest;

class TeacherController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            return redirect()->route('login.view');
        }
    }
    public function index()
    {

        $teachers = Teacher::with([
            'user',
            'teacherAssignments.course',
            'teacherAssignments.department',
            'teacherAssignments.academicSession',
            'teacherAssignments.semester'
        ])->latest()->get();

        return view('admin.lecturer.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.lecturer.store');
    }
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'other_name' => 'nullable|string|max:255',
            'phone' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string',
            'teaching_experience' => 'required|integer',
            'teacher_type' => 'required|string',
            'teacher_qualification' => 'required|string|max:255',
            'teacher_title' => 'required|string|max:255',
            'office_hours' => 'nullable|string|max:255',
            'office_address' => 'nullable|string|max:255',
            'biography' => 'nullable|string',
            'certifications' => 'nullable|array',
            'publications' => 'nullable|array',
            'number_of_awards' => 'nullable|integer',
            'date_of_employment' => 'required|date',
            'address' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'level' => 'required|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = User::create([
            'user_type' => User::TYPE_TEACHER,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'other_name' => $request->other_name,
            'username' => $request->first . '.' . $request->last_name,
            'slug' => Str::slug($request->first . '.' . $request->last_name),
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make('12345678'), // Change this to a generated password if needed
        ]);

        $teacher = new Teacher([
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'teaching_experience' => $request->teaching_experience,
            'teacher_type' => $request->teacher_type,
            'teacher_qualification' => $request->teacher_qualification,
            'teacher_title' => $request->teacher_title,
            'office_hours' => $request->office_hours,
            'office_address' => $request->office_address,
            'biography' => $request->biography,
            'certifications' => $request->certifications ? json_encode($request->certifications) : null,
            'publications' => $request->publications ? json_encode($request->publications) : null,
            'number_of_awards' => $request->number_of_awards,
            'date_of_employment' => $request->date_of_employment,
            'address' => $request->address,
            'nationality' => $request->nationality,
            'level' => $request->level,
            'employment_id' => str_shuffle(mt_rand(1000000, 9999999))
        ]);

        if ($request->hasFile('profile_photo')) {
            $profilePhoto = $request->file('profile_photo');
            $extension = $profilePhoto->getClientOriginalExtension();
            $profilePhotoName = time() . "." . $extension;
            $profilePhoto->move('admin/lecturers/profile/', $profilePhotoName);
            $user->profile_photo = 'admin/lecturers/profile/' . $profilePhotoName;
            $user->save();
        }

        $user->teacher()->save($teacher);

        return redirect()->route('admin.teacher.view')->with([
            'message' => 'Lecturer account created successfully.',
            'alert-type' => 'success'
        ]);
    }

    // view details of courses assign to the teacher
    public function courseDetails($courseId)
    {
        $course = Course::with(['departments', 'teachers'])->findOrFail($courseId);
        // dd($course);
        return view('admin.lecturer.courses.details', compact('course'));
    }


    public function show(Teacher $teacher)
    {

        $teacher->load([
            'user',
            'teacherAssignments' => function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id)
                    ->with(['course', 'department', 'academicSession', 'semester', 'courseAssignment', 'teacher']);
            },
            'teacherAssignments.course',
            'teacherAssignments.department',
            'teacherAssignments.academicSession',
            'teacherAssignments.semester',
            'teacherAssignments.courseAssignment',
            'teacherAssignments.teacher'
        ]);



        return view('admin.lecturer.detail', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        return view('admin.lecturer.edit', compact('teacher'));
    }
    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        // Check if a new profile photo has been uploaded
        if ($request->hasFile('profile_photo')) {
            // Get the old image path
            $old_image = $teacher->user->profile_photo;

            // Delete the old image if it exists
            if (!empty($old_image) && file_exists(public_path($old_image))) {
                unlink(public_path($old_image));
            }

            // Handle the new image upload
            $thumb = $request->file('profile_photo');
            $extension = $thumb->getClientOriginalExtension();
            $profilePhoto = time() . "." . $extension;
            $thumb->move('admin/lecturers/profile/', $profilePhoto);
            $teacher->user->profile_photo = 'admin/lecturers/profile/' . $profilePhoto;
        }

        // Update user data
        $teacher->user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'other_name' => $request->other_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'profile_photo' => $teacher->user->profile_photo ?? $teacher->user->profile_photo,
        ]);

        // Update teacher data
        $teacher->update([
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'teaching_experience' => $request->teaching_experience,
            'teacher_type' => $request->teacher_type,
            'teacher_qualification' => $request->teacher_qualification,
            'teacher_title' => $request->teacher_title,
            'office_hours' => $request->office_hours,
            'office_address' => $request->office_address,
            'biography' => $request->biography,
            'certifications' => $request->certifications,
            'publications' => $request->publications,
            'number_of_awards' => $request->number_of_awards,
            'date_of_employment' => $request->date_of_employment,
            'address' => $request->address,
            'nationality' => $request->nationality,
            'level' => $request->level,
        ]);

        $notification = [
            'message' => 'Teacher details updated successfully.',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }

    // public function destroy(Teacher $teacher)
    // {
    //     // Delete the teacher
    //     $teacher->delete();

    //     // Delete the user associated with the teacher
    //     $teacher->user->delete();

    //     $notification = [
    //         'message' => 'Teacher deleted successfully.',
    //         'alert-type' => 'danger'
    //     ];

    //     return redirect()->back()->with($notification);
    // }




    public function departmentDetails($departmentId, $teacherId)
    {
        $department = Department::with(['faculty'])->findOrFail($departmentId);
        $teacher = Teacher::findOrFail($teacherId);

        $teacherAssignments = TeacherAssignment::where('teacher_id', $teacherId)
            ->where('department_id', $departmentId)
            ->with(['course', 'academicSession', 'semester', 'courseAssignment'])
            ->get();



        $levels = $department->levels;

        return view('admin.lecturer.courses.department', compact('department', 'teacher', 'teacherAssignments', 'levels'));
    }

    public function viewRegisteredStudents($teacherId, $courseId, $semesterId, $academicSessionId)
    {
        //find if  teacher was really assigned to the course
        $assignment = TeacherAssignment::where('teacher_id', $teacherId)
            ->where('course_id', $courseId)
            ->where('semester_id', $semesterId)
            ->where('academic_session_id', $academicSessionId)
            ->firstOrFail();

        $enrollments = CourseEnrollment::where('course_id', $courseId)
            ->where('academic_session_id', $academicSessionId)
            ->whereHas('semesterCourseRegistration', function ($query) use ($semesterId, $academicSessionId) {
                $query->where('semester_id', $semesterId)
                    ->where('academic_session_id', $academicSessionId)
                    ->where('status', 'approved');
            })
            ->whereHas('student', function ($query) use ($assignment) {
                $query->where('department_id', $assignment->department_id);
            })
            ->with(['student.user', 'semesterCourseRegistration', 'studentScore'])
            ->get();

        $studentScores = StudentScore::where('course_id', $courseId)
            ->where('academic_session_id', $academicSessionId)
            ->where('semester_id', $semesterId)
            ->whereIn('student_id', $enrollments->pluck('student_id'))
            ->get()
            ->keyBy('student_id');

        return view('admin.lecturer.courses.registered_students', compact('assignment', 'enrollments'))->with('studentScores', $studentScores);
    }


    // public function storeScores(Request $request, $assignmentId)
    // {
    //     // dd($request->all());
    //     // dd($request);
    //     $assignment = TeacherAssignment::findOrFail($assignmentId);
    //     $validator = Validator::make($request->all(), [
    //         'scores.*.assessment' => 'required|numeric|min:0|max:40',
    //         'scores.*.exam' => 'required|numeric|min:0|max:60',
    //     ], [
    //         'scores.*.assessment.required' => 'Assessment score is required for all students.',
    //         'scores.*.exam.required' => 'Exam score is required for all students.',
    //         'scores.*.assessment.max' => 'Assessment score cannot exceed 40.',
    //         'scores.*.exam.max' => 'Exam score cannot exceed 60.',
    //     ]);
    //     if ($validator->fails()) {
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }


    //     try {
    //         DB::beginTransaction();

    //         foreach ($request->scores as $enrollmentId => $scoreData) {
    //             $enrollment = CourseEnrollment::findOrFail($enrollmentId);

    //             // $totalScore = floatval($scoreData['assessment']) + floatval($scoreData['exam']);
    //             // $grade = GradeSystem::getGrade($totalScore);
    //             // $isFailed = $grade === 'F';

    //             $assessmentScore = floatval($scoreData['assessment']);
    //             $examScore = floatval($scoreData['exam']);
    //             $totalScore = $assessmentScore + $examScore;
    //             $grade = GradeSystem::getGrade($totalScore);
    //             $isFailed = $grade === 'F';


    //             StudentScore::updateOrCreate(
    //                 [
    //                     'student_id' => $enrollment->student_id,
    //                     'course_id' => $assignment->course_id,
    //                     'academic_session_id' => $assignment->academic_session_id,
    //                     'semester_id' => $assignment->semester_id,
    //                 ],
    //                 [
    //                     'teacher_id' => $assignment->teacher_id,
    //                     'department_id' => $enrollment->student->department_id,
    //                     'assessment_score' => $assessmentScore,
    //                     'exam_score' => $examScore,
    //                     'total_score' => $totalScore,
    //                     'grade' => $grade,
    //                     'is_failed' => $isFailed,
    //                 ]
    //             );
    //         }

    //         DB::commit();
    //         return redirect()->back()->with('success', 'Scores have been saved successfully.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error saving scores: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'An error occurred while saving scores. Please try again.');
    //     }
    // }

    // public function storeScores(Request $request, $assignmentId)
    // {
    //     $assignment = TeacherAssignment::findOrFail($assignmentId);
    //     $validator = Validator::make($request->all(), [
    //         'scores.*.assessment' => 'required|numeric|min:0|max:40',
    //         'scores.*.exam' => 'required|numeric|min:0|max:60',
    //     ], [
    //         'scores.*.assessment.required' => 'Assessment score is required for all students.',
    //         'scores.*.exam.required' => 'Exam score is required for all students.',
    //         'scores.*.assessment.max' => 'Assessment score cannot exceed 40.',
    //         'scores.*.exam.max' => 'Exam score cannot exceed 60.',
    //     ]);

    //     if ($validator->fails()) {
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }

    //     try {
    //         DB::beginTransaction();

    //         foreach ($request->scores as $enrollmentId => $scoreData) {
    //             $enrollment = CourseEnrollment::findOrFail($enrollmentId);

    //             $assessmentScore = floatval($scoreData['assessment']);
    //             $examScore = floatval($scoreData['exam']);
    //             $totalScore = $assessmentScore + $examScore;
    //             $grade = GradeSystem::getGrade($totalScore);
    //             $isFailed = $grade === 'F';

    //             StudentScore::updateOrCreate(
    //                 [
    //                     'student_id' => $enrollment->student_id,
    //                     'course_id' => $assignment->course_id,
    //                     'academic_session_id' => $assignment->academic_session_id,
    //                     'semester_id' => $assignment->semester_id,
    //                 ],
    //                 [
    //                     'teacher_id' => $assignment->teacher_id,
    //                     'department_id' => $enrollment->student->department_id,
    //                     'assessment_score' => $assessmentScore,
    //                     'exam_score' => $examScore,
    //                     'total_score' => $totalScore,
    //                     'grade' => $grade,
    //                     'is_failed' => $isFailed,
    //                 ]
    //             );
    //         }

    //         DB::commit();
    //         return redirect()->back()->with('success', 'Scores have been saved successfully.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error saving scores: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'An error occurred while saving scores. Please try again.');
    //     }
    // }

    public function storeScores(Request $request, $assignmentId)
    {
        $assignment = TeacherAssignment::findOrFail($assignmentId);
        $validator = Validator::make($request->all(), [
            'scores.*.assessment' => 'required',
            'scores.*.exam' => 'required',
        ], [
            'scores.*.assessment.required' => 'Assessment score is required for all students.',
            'scores.*.exam.required' => 'Exam score is required for all students.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            foreach ($request->scores as $enrollmentId => $scoreData) {
                $enrollment = CourseEnrollment::findOrFail($enrollmentId);

                $assessmentScore = floatval($this->extractScore($scoreData['assessment']));
                // dd($assessmentScore);
                $examScore = floatval($this->extractScore($scoreData['exam']));
                // dd($examScore);

                if ($assessmentScore > 40 || $examScore > 60) {
                    throw new \Exception("Invalid score range for enrollment ID: $enrollmentId");
                }

                $totalScore = $assessmentScore + $examScore;
                // dd($totalScore);

                $grade = GradeSystem::getGrade($totalScore);
                $isFailed = $grade === 'F';

                StudentScore::updateOrCreate(
                    [
                        'student_id' => $enrollment->student_id,
                        'course_id' => $assignment->course_id,
                        'academic_session_id' => $assignment->academic_session_id,
                        'semester_id' => $assignment->semester_id,
                    ],
                    [
                        'teacher_id' => $assignment->teacher_id,
                        'department_id' => $enrollment->student->department_id,
                        'assessment_score' => $assessmentScore,
                        'exam_score' => $examScore,
                        'total_score' => floatval($totalScore),
                        'grade' => $grade,
                        'is_failed' => $isFailed,
                    ]
                );

                Log::debug('Score data', [
                    'enrollment_id' => $enrollmentId,
                    'student_id' => $enrollment->student_id,
                    'assessment_score' => $assessmentScore,
                    'exam_score' => $examScore,
                    'total_score' => $totalScore,
                    'grade' => $grade,
                    'is_failed' => $isFailed,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Scores have been saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving scores: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while saving scores. Please try again.');
        }
    }

    private function extractScore($scoreData)
    {
        if (is_array($scoreData)) {
            return isset($scoreData['to']) ? floatval($scoreData['to']) : floatval($scoreData[0]);
        }
        return floatval($scoreData);
    }

    public function exportScores($assignmentId)
    {
        $assignment = TeacherAssignment::findOrFail($assignmentId);
        $enrollments = CourseEnrollment::where('course_id', $assignment->course_id)
            ->where('academic_session_id', $assignment->academic_session_id)
            ->whereHas('semesterCourseRegistration', function ($query) use ($assignment) {
                $query->where('semester_id', $assignment->semester_id)
                    ->where('academic_session_id', $assignment->academic_session_id)
                    ->where('status', 'approved');
            })
            ->with(['student.user', 'studentScore'])
            ->get();

        $csv = Writer::createFromString('');
        $csv->insertOne(['Student ID', 'Name', 'Assessment Score', 'Exam Score']);

        foreach ($enrollments as $enrollment) {
            $csv->insertOne([
                $enrollment->student->matric_number,
                $enrollment->student->user->fullName(),
                $enrollment->studentScore->assessment_score ?? '',
                $enrollment->studentScore->exam_score ?? ''
            ]);
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="scores.csv"',
        ];

        return response($csv->getContent(), 200, $headers);
    }

    public function importScores(Request $request, $assignmentId)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $assignment = TeacherAssignment::findOrFail($assignmentId);

        $csv = Reader::createFromPath($request->file('csv_file')->getPathname());
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        DB::beginTransaction();

        try {
            foreach ($records as $record) {
                $student = Student::where('matric_number', $record['Student ID'])->firstOrFail();

                $totalScore = $record['Assessment Score'] + $record['Exam Score'];
                $grade = GradeSystem::getGrade($totalScore);
                $isFailed = $grade === 'F';

                StudentScore::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'course_id' => $assignment->course_id,
                        'academic_session_id' => $assignment->academic_session_id,
                        'semester_id' => $assignment->semester_id,
                    ],
                    [
                        'teacher_id' => $assignment->teacher_id,
                        'department_id' => $student->department_id,
                        'assessment_score' => $record['Assessment Score'],
                        'exam_score' => $record['Exam Score'],
                        'total_score' => $totalScore,
                        'grade' => $grade,
                        'is_failed' => $isFailed,
                    ]
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'Scores have been imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while importing scores: ' . $e->getMessage());
        }
    }


    public function viewAudits(Teacher $teacher)
    {
        $groupedAudits = $teacher->scoreAudits()
            ->join('academic_sessions', 'student_scores.academic_session_id', '=', 'academic_sessions.id')
            ->join('semesters', 'student_scores.semester_id', '=', 'semesters.id')
            ->join('courses', 'student_scores.course_id', '=', 'courses.id')
            ->select(
                'score_audits.*',
                'academic_sessions.name as session_name',
                'semesters.name as semester_name',
                'courses.title as course_title'
            )
            ->orderBy('academic_sessions.name', 'desc')
            ->orderBy('semesters.name', 'asc')
            ->get()
            ->groupBy(['session_name', 'semester_name', 'course_title']);

        return view('admin.lecturer.courses.audits', compact('teacher', 'groupedAudits'));
    }
}
