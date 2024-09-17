<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Semester;
use Barryvdh\DomPDF\PDF;
use App\Models\TimeTable;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Exports\TimetableExport;
use App\Models\CourseAssignment;
use App\Exports\TimetablesExport;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\TimetableApproved;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TimetableSubmittedForApproval;

class AdminTimeTableController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    // public function index()
    // {
    //     $timetables = Timetable::with(['academicSession', 'semester', 'department', 'course', 'teacher'])
    //         ->where('status', TimeTable::STATUS_APPROVED)
    //         ->orderBy('day_of_week')
    //         ->orderBy('start_time')
    //         ->get();

    //     return view('admin.timeTable.index', compact('timetables'));
    // }

    public function index()
    {
        $timetables = Timetable::with(['academicSession', 'semester', 'department', 'course', 'teacher'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $semesters = Semester::all();

        return view('admin.timeTable.index', compact('timetables', 'semesters'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $academicSessions = AcademicSession::all();
        $semesters = Semester::all();
        $departments = Department::all();
        $courses = Course::all();
        $teachers = Teacher::all();

        return view('admin.timeTable.create', compact('academicSessions', 'semesters', 'departments', 'courses', 'teachers'));
    }

    public function getDepartmentLevels(Department $department)
    {
        return response()->json($department->levels);
    }
    public function getCourses(Request $request)
    {
        $departmentId = $request->input('department_id');
        $level = $request->input('level');

        return CourseAssignment::where('department_id', $departmentId)
            ->where('level', $level)
            ->with('course')
            ->get()
            ->pluck('course');
    }

    public function getCourseAssignment(Request $request)
    {
        $courseId = $request->input('course_id');
        $departmentId = $request->input('department_id');
        $level = $request->input('level');

        $assignment = CourseAssignment::where('course_id', $courseId)
            ->where('department_id', $departmentId)
            ->where('level', $level)
            ->with('teacherAssignments.teacher.user')
            ->first();

        return response()->json([
            'teacher_name' => $assignment->teacherAssignment->teacher->user->full_name ?? null,
            'teacher_id' => $assignment->teacherAssignment->teacher->id ?? null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'semester_id' => 'required|exists:semesters,id',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|integer|',
            'day_of_week' => 'required|integer|min:1|max:7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'room' => 'required|string|max:255',
            'class_duration' => 'required|integer|min:1',
            "class_date" => 'required|date'
        ]);

        $validatedData['status'] = TimeTable::STATUS_DRAFT;
        $validatedData['created_by'] = auth()->id(); // Track who created

        $newEntry = new TimeTable($validatedData);

        $conflicts = $this->checkConflicts($newEntry);

        if ($conflicts->isNotEmpty()) {
            return back()->withInput()->withErrors(['conflict' => 'There is a scheduling conflict.']);
        }

        $newEntry->save();

        return redirect()->route('admin.timetable.view')->with('success', 'Timetable entry created successfully.');
    }

    public function draftIndex()
    {
        $draftTimetables = Timetable::with(['academicSession', 'semester', 'department', 'course', 'teacher'])
            ->whereIn('status', [TimeTable::STATUS_DRAFT, TimeTable::STATUS_PENDING_APPROVAL])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('admin.timeTable.draft_timetables', compact('draftTimetables'));
    }

    public function submitForApproval(TimeTable $timetable)
    {
        $timetable->status = TimeTable::STATUS_PENDING_APPROVAL;
        $timetable->save();

        // Notify approvers
        $approvers = User::role('timetable_approver')->get();
        Notification::send($approvers, new TimetableSubmittedForApproval($timetable));

        return redirect()->route('admin.timetable.draftIndex')->with('success', 'Timetable submitted for approval.');
    }

    public function archive(TimeTable $timetable)
    {
        $timetable->status = TimeTable::STATUS_ARCHIVED;
        $timetable->save();

        return redirect()->route('admin.timetable.draftIndex')->with('success', 'Timetable archived successfully.');
    }

    private function checkConflicts(TimeTable $newEntry)
    {
        return TimeTable::where('semester_id', $newEntry->semester_id)
            ->where('day_of_week', $newEntry->day_of_week)
            ->where(function ($query) use ($newEntry) {
                $query->whereBetween('start_time', [$newEntry->start_time, $newEntry->end_time])
                    ->orWhereBetween('end_time', [$newEntry->start_time, $newEntry->end_time])
                    ->orWhere(function ($q) use ($newEntry) {
                        $q->where('start_time', '<=', $newEntry->start_time)
                            ->where('end_time', '>=', $newEntry->end_time);
                    });
            })
            ->where(function ($query) use ($newEntry) {
                $query->where('room', $newEntry->room)
                    ->orWhere('teacher_id', $newEntry->teacher_id)
                    ->orWhere(function ($q) use ($newEntry) {
                        $q->where('course_id', $newEntry->course_id)
                            ->where('department_id', $newEntry->department_id)
                            ->where('level', $newEntry->level);
                    });
            })
            ->get();
    }

    public function show(TimeTable $timetable)
    {
        return view('admin.timeTable.show', compact('timetable'));
    }

    public function edit(TimeTable $timetable)
    {
        $academicSessions = AcademicSession::all();
        $semesters = Semester::all();
        $departments = Department::all();
        $courses = Course::all();
        $teachers = Teacher::all();

        return view('admin.timeTable.edit', compact('timetable', 'academicSessions', 'semesters', 'departments', 'courses', 'teachers'));
    }

    public function update(Request $request, TimeTable $timetable)
    {
        $validatedData = $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'semester_id' => 'required|exists:semesters,id',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|integer|min:1|max:6',
            'day_of_week' => 'required|integer|min:1|max:7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
            'room' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $timetable->fill($validatedData);

        $conflicts = $this->checkConflicts($timetable);

        if ($conflicts->isNotEmpty()) {
            return back()->withInput()->withErrors(['conflict' => 'There is a scheduling conflict.']);
        }

        $timetable->save();

        return redirect()->route('admin.timetable.view')->with('success', 'Timetable entry updated successfully.');
    }

    public function destroy(TimeTable $timetable)
    {
        $timetable->delete();
        return redirect()->route('admin.timetable.view')->with('success', 'Timetable entry deleted successfully.');
    }
    // 1. Bulk actions for approving or archiving multiple timetables
    public function bulkApprove(Request $request)
    {
        $timetableIds = $request->input('timetable_ids');
        $timetables = TimeTable::whereIn('id', $timetableIds)->get();

        foreach ($timetables as $timetable) {
            $timetable->status = TimeTable::STATUS_APPROVED;
            $timetable->save();

            // Notify relevant users
            //   $timetable->department->head->notify(new BulkTimetableApproved($timetable));
        }

        return redirect()->back()->with('success', 'Timetables approved in bulk.');
    }

    public function approverDashboard()
    {
        $pendingTimetables = TimeTable::where('status', TimeTable::STATUS_PENDING_APPROVAL)
            ->with(['department', 'semester', 'academicSession'])
            ->get();

        return view('admin.timetable.approver_dashboard', compact('pendingTimetables'));
    }

    //use this to view a students timetable from the admin section
    public function studentView(Request $request)
    {
        $student = auth()->user()->student; // . this will change
        $timetables = TimeTable::where('department_id', $student->department_id)
            ->where('level', $student->level)
            ->where('status', TimeTable::STATUS_APPROVED)
            ->with(['course', 'teacher'])
            ->get();

        return view('student.timetable', compact('timetables'));
    }



    // 3. Version history for timetables
    public function versionHistory($id)
    {
        $timetable = TimeTable::findOrFail($id);
        $versions = $timetable->versions()->orderBy('created_at', 'desc')->get();

        return view('admin.timetable.version_history', compact('timetable', 'versions'));
    }

    // 4. Public view for students
    public function publicView(Request $request)
    {
        $department = Department::findOrFail($request->input('department_id'));
        $level = $request->input('level');

        $timetables = TimeTable::where('department_id', $department->id)
            ->where('level', $level)
            ->where('status', TimeTable::STATUS_APPROVED)
            ->with(['course', 'teacher'])
            ->get();

        return view('public.timetable', compact('timetables', 'department', 'level'));
    }

    // 5. Integration with calendar system (e.g., Google Calendar)
    //   public function exportToGoogleCalendar($id)
    //   {
    //       $timetable = TimeTable::findOrFail($id);

    //       // Implement Google Calendar API integration here
    //       // This is a placeholder for the actual implementation
    //       $googleCalendar = new GoogleCalendarIntegration();
    //       $googleCalendar->exportTimetable($timetable);

    //       return redirect()->back()->with('success', 'Timetable exported to Google Calendar.');
    //   }

    // 6. Clone timetables from previous semesters
    public function cloneTimetable(Request $request)
    {
        $sourceTimetableId = $request->input('source_timetable_id');
        $targetSemesterId = $request->input('target_semester_id');

        $sourceTimetable = TimeTable::findOrFail($sourceTimetableId);
        $newTimetable = $sourceTimetable->replicate();
        $newTimetable->semester_id = $targetSemesterId;
        $newTimetable->status = TimeTable::STATUS_DRAFT;
        $newTimetable->save();

        return redirect()->route('admin.timetable.edit', $newTimetable->id)
            ->with('success', 'Timetable cloned successfully. You can now edit the new draft.');
    }

    // Additional method for exporting timetables
    public function export(Request $request)
    {
        $format = $request->input('format', 'xlsx');
        $timetableId = $request->input('timetable_id');

        $timetable = TimeTable::findOrFail($timetableId);

        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($timetable);
            case 'csv':
                return Excel::download(new TimetableExport($timetable), 'timetable.csv');
            case 'xlsx':
            default:
                return Excel::download(new TimetableExport($timetable), 'timetable.xlsx');
        }
    }

    private function exportToPdf(TimeTable $timetable)
    {
        // Implement PDF export logic here
        //  $n = FacadePdf;
        $pdf = FacadePdf::loadView('exports.timetable_pdf', compact('timetable'));
        return $pdf->download('timetable.pdf');
    }


    // private function checkConflicts(TimeTable $newEntry)
    // {
    //     return TimeTable::where('semester_id', $newEntry->semester_id)
    //         ->where('day_of_week', $newEntry->day_of_week)
    //         ->where(function ($query) use ($newEntry) {
    //             $query->whereBetween('start_date', [$newEntry->start_date, $newEntry->end_date])
    //                 ->orWhereBetween('end_date', [$newEntry->start_date, $newEntry->end_date])
    //                 ->orWhere(function ($q) use ($newEntry) {
    //                     $q->where('start_date', '<=', $newEntry->start_date)
    //                         ->where('end_date', '>=', $newEntry->end_date);
    //                 });
    //         })
    //         ->get()
    //         ->filter(function ($entry) use ($newEntry) {
    //             return $newEntry->hasConflict($entry);
    //         });
    // }




    // public function getCalendarData(Request $request)
    // {
    //     $semester = Semester::findOrFail($request->input('semester_id'));
    //     $startDate = Carbon::parse($semester->start_date);
    //     $endDate = Carbon::parse($semester->end_date);
    //     $timetables = TimeTable::where('semester_id', $semester->id)
    //         ->with(['course', 'teacher.user', 'department'])
    //         ->get();

    //     $events = $timetables->map(function ($timetable) use ($startDate, $endDate) {
    //         $startTime = Carbon::parse($timetable->start_time)->format('H:i:s');
    //         $endTime = Carbon::parse($timetable->end_time)->format('H:i:s');

    //         return [
    //             'id' => $timetable->id,
    //             'title' => $timetable->course->code . ' - ' . $timetable->teacher->user->full_name,
    //             'startTime' => $startTime,
    //             'endTime' => $endTime,
    //             'startRecur' => $startDate->format('Y-m-d'),
    //             'endRecur' => $endDate->format('Y-m-d'),
    //             'daysOfWeek' => [(int)$timetable->day_of_week - 1], // Assuming day_of_week is 1-7, convert to 0-6
    //             'color' => $this->getColorForDepartment($timetable->department_id),
    //             'textColor' => '#66e',
    //             'extendedProps' => [
    //                 'department' => $timetable->department->name,
    //                 'room' => $timetable->room,
    //                 'course_name' => $timetable->course->name ?? 'N/A',
    //                 'teacher_name' => $timetable->teacher->user->full_name,
    //             ],
    //         ];
    //     });

    //     return response()->json($events);
    // }
    public function getCalendarData(Request $request)
    {
        $semester = Semester::findOrFail($request->input('semester_id'));
        $startDate = Carbon::parse($semester->start_date);
        $endDate = Carbon::parse($semester->end_date);
        $timetables = TimeTable::where('semester_id', $semester->id)
            ->with(['course', 'teacher.user', 'department'])
            ->get();

        $events = $timetables->map(function ($timetable) use ($startDate, $endDate) {
            $startTime = Carbon::parse($timetable->start_time)->format('H:i:s');
            $endTime = Carbon::parse($timetable->end_time)->format('H:i:s');

            $color = $this->getColorForDepartment($timetable->department_id);
            Log::info("Department ID: {$timetable->department_id}, Color: {$color}");

            return [
                'id' => $timetable->id,
                'title' => $timetable->course->code . ' - ' . $timetable->teacher->user->full_name,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'startRecur' => $startDate->format('Y-m-d'),
                'endRecur' => $endDate->format('Y-m-d'),
                'daysOfWeek' => [(int)$timetable->day_of_week - 1], // Assuming day_of_week is 1-7, convert to 0-6
                'textColor' => '#66e',
                'extendedProps' => [
                    'department' => $timetable->department->name,
                    'room' => $timetable->room,
                    'course_name' => $timetable->course->name ?? 'N/A',
                    'teacher_name' => $timetable->teacher->user->full_name,
                    'color' => $color, // Add the color to extendedProps
                ],
            ];
        });

        return response()->json($events);
    }
    private function getColorForDepartment($departmentId)
    {
        $colors = [
            '#FF5733',
            '#33FF57',
            '#3357FF',
            '#FF33F5',
            '#33FFF5',
            '#F5FF33',
            '#FF3333',
            '#33FF33',
            '#3333FF',
            '#FF33F5',
            '#33FFFF',
            '#FF33FF'
        ];

        // Use modulo to ensure we always have a valid index, even if we have more departments than colors
        return $colors[$departmentId % count($colors)];
    }




    // public function submitForApproval(TimeTable $timetable)
    // {
    //     $timetable->status = TimeTable::STATUS_PENDING_APPROVAL;
    //     $timetable->save();

    //     // Notify approvers
    //     $approvers = User::role('timetable_approver')->get();
    //     Notification::send($approvers, new TimetableSubmittedForApproval($timetable));

    //     return redirect()->route('admin.timetable.view')->with('success', 'Timetable submitted for approval.');
    // }

    public function approve(TimeTable $timetable)
    {
        $timetable->status = TimeTable::STATUS_APPROVED;
        $timetable->approved_by = auth()->id();
        $timetable->save();

        // Notify relevant users
        $timetable->teacher->user->notify(new TimetableApproved($timetable));
        // Notify students (you might want to implement this differently based on your user structure)
        $students = User::whereHas('student', function ($query) use ($timetable) {
            $query->where('department_id', $timetable->department_id)
                ->where('level', $timetable->level);
        })->get();
        Notification::send($students, new TimetableApproved($timetable));

        return redirect()->route('admin.timetable.view')->with('success', 'Timetable approved successfully.');
    }

    // public function archive(TimeTable $timetable)
    // {
    //     $timetable->status = TimeTable::STATUS_ARCHIVED;
    //     $timetable->save();

    //     return redirect()->route('admin.timetable.view')->with('success', 'Timetable archived successfully.');
    // }


    // public function export(Request $request, $format)
    // {
    //     $timetables = TimeTable::with(['academicSession', 'semester', 'department', 'course', 'teacher'])
    //         ->where('status', TimeTable::STATUS_APPROVED)
    //         ->orderBy('day_of_week')
    //         ->orderBy('start_time')
    //         ->get();

    //     switch ($format) {
    //         case 'pdf':
    //             $pdf = PDF::loadView('exports.timetable', compact('timetables'));
    //             return $pdf->download('timetable.pdf');
    //         case 'excel':
    //             return Excel::download(new TimetablesExport($timetables), 'timetable.xlsx');
    //         case 'csv':
    //             return Excel::download(new TimetablesExport($timetables), 'timetable.csv');
    //         default:
    //             abort(404);
    //     }
    // }

    public function printView()
    {
        $timetables = TimeTable::with(['academicSession', 'semester', 'department', 'course', 'teacher'])
            ->where('status', TimeTable::STATUS_APPROVED)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('admin.timeTable.print', compact('timetables'));
    }



    public function bulkCreate()
    {
        $academicSessions = AcademicSession::all();
        $semesters = Semester::all();
        $departments = Department::all();
        $courses = Course::all();
        $teachers = Teacher::all();

        return view('timetables.bulk_create', compact('academicSessions', 'semesters', 'departments', 'courses', 'teachers'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'semester_id' => 'required|exists:semesters,id',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|integer|min:1|max:6',
            'entries' => 'required|array',
            'entries.*.day_of_week' => 'required|integer|min:1|max:7',
            'entries.*.start_time' => 'required|date_format:H:i',
            'entries.*.end_time' => 'required|date_format:H:i|after:entries.*.start_time',
            'entries.*.course_id' => 'required|exists:courses,id',
            'entries.*.teacher_id' => 'required|exists:teachers,id',
            'entries.*.room' => 'required|string|max:255',
        ]);

        $commonData = $request->only(['academic_session_id', 'semester_id', 'department_id', 'level']);

        foreach ($request->entries as $entry) {
            $newEntry = new Timetable(array_merge($commonData, $entry));

            $conflicts = Timetable::where('semester_id', $newEntry->semester_id)
                ->where('day_of_week', $newEntry->day_of_week)
                ->get()
                ->filter(function ($existingEntry) use ($newEntry) {
                    return $newEntry->hasConflict($existingEntry);
                });

            if ($conflicts->isNotEmpty()) {
                return back()->withInput()->withErrors(['conflict' => 'There is a scheduling conflict.']);
            }

            $newEntry->save();
        }

        return redirect()->route('admin.timetable.view')->with('success', 'Bulk timetable entries created successfully.');
    }
    public function viewByDepartment(Request $request)
    {
        $departments = Department::all();
        $selectedDepartment = $request->input('department_id');
        $selectedLevel = $request->input('level');

        $timetables = collect();

        if ($selectedDepartment && $selectedLevel) {
            $timetables = Timetable::with(['academicSession', 'semester', 'department', 'course', 'teacher'])
                ->where('department_id', $selectedDepartment)
                ->where('level', $selectedLevel)
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get();
        }

        return view('admin.timetables.by_department', compact('departments', 'selectedDepartment', 'selectedLevel', 'timetables'));
    }

    public function viewByTeacher(Request $request)
    {
        $teachers = Teacher::all();
        $selectedTeacher = $request->input('teacher_id');

        $timetables = collect();

        if ($selectedTeacher) {
            $timetables = Timetable::with(['academicSession', 'semester', 'department', 'course', 'teacher'])
                ->where('teacher_id', $selectedTeacher)
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get();
        }

        return view('admin.timetables.by_teacher', compact('teachers', 'selectedTeacher', 'timetables'));
    }
}
