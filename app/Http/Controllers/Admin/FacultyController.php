<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faculty;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\FacultyRequest;

class FacultyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $faculties = Faculty::with('departments')->oldest()->get();
        return view('admin.faculties.index', compact('faculties'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(FacultyRequest $request)
    {
        $faculty = Faculty::create($request->validated());
        $notification = [
            'message' => 'Faculty Created Successfully!!',
            'alert-type' => 'success'
        ];
        return response()->json([
            'notification' => $notification,
            'faculty' => $faculty
        ]);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(FacultyRequest $request,  $faculty)
    {
        $faculty = Faculty::findOrFail($faculty);
        $faculty->update($request->validated());
        $notification = [
            'message' => 'fac$faculty Updated Successfully!!',
            'alert-type' => 'success'
        ];

        return response()->json([
            'notification' => $notification,
            'fac$faculty' => $faculty
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $user = Auth::user();
        // ! remember come here for creating traits or middleware or gate
        if (!$user->isAdmin()) {
            $notification = [
                'message' => 'You are not authorized to delete this course!',
                'alert-type' => 'error'
            ];
            return redirect()->back()->with($notification);
        }
        $faculty = Faculty::findOrFail($id);

        $faculty->delete();
        $notification = [
            'message' => 'Faculty Deleted Successfully!!',
            'alert-type' => 'success'
        ];
        return redirect()->back()->with($notification);
    }
}
