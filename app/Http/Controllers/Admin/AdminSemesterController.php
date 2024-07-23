<?php

namespace App\Http\Controllers\Admin;

use App\Models\Semester;
use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Http\Controllers\Controller;

class AdminSemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $semesters = Semester::with('academicSession')->latest()->get();
        return view('admin.semester.index', compact('semesters'));
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
        $request->validate([
            'name' => 'required|unique:semesters',
            'season' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'is_current' => 'boolean',
        ]);

        $semester = Semester::create($request->all());

        if ($request->is_current) {
            Semester::where('id', '!=', $semester->id)->update(['is_current' => false]);
        }


        return redirect()->route('semester-manager.index')->with('success', 'Semester created successfully.');
    }



    /**
     * Display the specified resource.
     */
    public function show(Semester $semester)
    {
        //
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
        try {
            // dd($semester_manager);
            $request->validate([
                'name' => 'required|unique:semesters,name,' . $semester_manager->id,
                'season' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'academic_session_id' => 'required|exists:academic_sessions,id',
                'is_current' => 'boolean',
            ]);

            $semester_manager->update($request->all());

            if ($request->is_current) {
                Semester::where('id', '!=', $semester_manager->id)->update(['is_current' => false]);
            }
    

            $notification = [
                'message' => 'Semester updated successfully.',
                'alert-type' => 'success'
            ];


            return redirect()->route('semester-manager.index')->with($notification);
        } catch (\Exception $e) {
            $notification = [
                'message' => $e->getMessage(),
                'alert-type' => 'error'
            ];
            return redirect()->route('semester-manager.index')->with($notification);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Semester $semester)
    {
        $semester->delete();

        $notification = [
            'message' => 'Semester deleted successfully.',
            'alert-type' => 'success'
        ];


        return redirect()->route('semester-manager.index')->with($notification);
    }
}
