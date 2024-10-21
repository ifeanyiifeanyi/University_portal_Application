<?php

namespace App\Http\Controllers\Admin;

use App\Models\Semester;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class AdminSemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $academicSessions = AcademicSession::all();
        $semesters = Semester::with('academicSession')->latest()->paginate(10);
        return view('admin.semester.index', compact('semesters', 'academicSessions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function show(Request $request)
    {
        $query = Semester::with('academicSession');

        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('season', 'LIKE', "%{$searchTerm}%")
                ->orWhereHas('academicSession', function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%");
                });
        }

        $semesters = $query->latest()->paginate(10);
        $academicSessions = AcademicSession::all();
        return view('admin.semester.index', compact('semesters', 'academicSessions'));
    }

    private function validateSemester(Request $request, $semesterId = null)
    {
        $rules = [
            'name' => ['required', 'unique:semesters,name,' . $semesterId],
            'season' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'is_current' => 'boolean',
        ];

        $validatedData = $request->validate($rules);

        // Check for overlapping dates
        $overlappingSemesters = Semester::where('academic_session_id', $request->academic_session_id)
            ->where(function ($query) use ($request, $semesterId) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                    });
                if ($semesterId) {
                    $query->where('id', '!=', $semesterId);
                }
            })->exists();

        if ($overlappingSemesters) {
            throw ValidationException::withMessages(['date_overlap' => 'The semester dates overlap with an existing semester in the same academic session.']);
        }

        return $validatedData;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currentSession = AcademicSession::where('is_current', true)->first();
        $academicSessions = AcademicSession::all();

        return view('admin.semester.create', compact('academicSessions', 'currentSession'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateSemester($request);
        $semester = Semester::create($validatedData);

        if ($request->is_current) {
            Semester::where('id', '!=', $semester->id)->update(['is_current' => false]);
        }


        return redirect()->route('semester-manager.index')->with('success', 'Semester created successfully.');
    }


    /**
     * perform bulk delete/action
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,change_session',
            'semesters' => 'required|array',
            'semesters.*' => 'exists:semesters,id',
            'new_session' => 'required_if:action,change_session|exists:academic_sessions,id',
        ]);

        $semesters = Semester::whereIn('id', $request->semesters)->get();

        if ($request->action === 'delete') {
            $deletedCount = 0;
            foreach ($semesters as $semester) {
                if (!$semester->is_current && !$semester->courseAssignments()->exists() && !$semester->teacherAssignments()->exists()) {
                    $semester->delete();
                    $deletedCount++;
                }
            }
            $message = "$deletedCount semester(s) have been deleted. ";
            if ($deletedCount < count($request->semesters)) {
                $message .= (count($request->semesters) - $deletedCount) . " semester(s) could not be deleted due to being current or having associated assignments.";
            }
        } else {
            Semester::whereIn('id', $request->semesters)->update(['academic_session_id' => $request->new_session]);
            $message = 'Academic session changed for selected semesters.';
        }

        return redirect()->route('semester-manager.index')->with('success', $message);
    }

    public function toggleCurrent(Semester $semester_manager)
    {
        $semester_manager->is_current = !$semester_manager->is_current;
        $semester_manager->save();

        if ($semester_manager->is_current) {

            Semester::where('id', '!=', $semester_manager->id)->update(['is_current' => false]);
        }

        return redirect()->route('semester-manager.index')->with('success', 'Semester status updated successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Semester $semester_manager)
    {
        $currentSession = AcademicSession::where('is_current', true)->first();
        $academicSessions = AcademicSession::all();

        return view('admin.semester.edit', compact('semester_manager', 'academicSessions', 'currentSession'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Semester $semester_manager)
    {

        // dd($semester_manager);
        $validatedData = $request->validate([
            'name' => ['required', 'unique:semesters,name,' . $semester_manager->id],
            'season' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'is_current' => 'boolean',
        ]);
        $semester_manager->update($validatedData);

        if ($request->is_current) {
            Semester::where('id', '!=', $semester_manager->id)->update(['is_current' => false]);
        }

        return redirect()->route('semester-manager.index')->with([
            'message' => 'Semester updated successfully',
            'alert-type' => 'success'
        ]);

    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Semester $semester_manager)
    {
        // Check if the semester is current
        if ($semester_manager->is_current) {
            $notification = [
                'message' => 'Cannot delete the current semester.',
                'alert-type' => 'error'
            ];
            return redirect()->back()->with($notification);
        }

        // Check if the semester has any associated assignments
        if ($semester_manager->courseAssignments()->exists() || $semester_manager->teacherAssignments()->exists()) {
            $notification = [
                'message' => 'Cannot delete the semester as it has associated assignments.',
                'alert-type' => 'error'
            ];
            return redirect()->back()->with($notification);
        }


        $semester_manager->delete();

        $notification = [
            'message' => 'Semester deleted successfully.',
            'alert-type' => 'success'
        ];


        return redirect()->route('semester-manager.index')->with($notification);
    }


}
