<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AcademicSession;
use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\createAcademicSessionRequest;

class AcademicSessionController extends Controller
{
    public function index()
    {
        $academicSessions = AcademicSession::simplePaginate(100);
        return view('admin.academicSession.index', compact('academicSessions'));
    }


    public function store(createAcademicSessionRequest $request)
    {
        $validatedData = $request->validated();
        // Check if there is already an existing session with is_current set to true
        if ($validatedData['is_current'] && AcademicSession::where('is_current', true)->exists()) {
            $notification = [
                'message' => 'There is already a current session!',
                'alert-type' => 'error'
            ];

            return redirect()->back()->with($notification);
        }

        // Save the data to your model
        $academicSession = AcademicSession::create($validatedData);
        ActivityLogHelper::logAcademicSessionActivity('Created', $academicSession);

        $notification = [
            'message' => 'Session Created!!',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }


    public function edit($id)
    {
        $academicSessions = AcademicSession::simplePaginate(100);
        $academicSessionSingle = AcademicSession::find($id);


        return view('admin.academicSession.index', compact('academicSessionSingle', 'academicSessions'));
    }

    public function update($id, createAcademicSessionRequest $request)
    {
        $validatedData = $request->validated();

        $session = AcademicSession::findOrFail($id);
        // Check if there is already an existing session with is_current set to true
        if ($validatedData['is_current'] && AcademicSession::where('is_current', true)->exists()) {
            $notification = [
                'message' => 'There is already a current session!',
                'alert-type' => 'error'
            ];

            return redirect()->back()->with($notification);
        }

        // Save the data to your model
        $session->update($validatedData);
        ActivityLogHelper::logAcademicSessionActivity('Updated', $session);

        $notification = [
            'message' => 'Session Updated!!',
            'alert-type' => 'success'
        ];
        return redirect()->route('admin.academic.session')->with($notification);
    }

    public function destroy($id)
    {
        $session = AcademicSession::findOrFail($id);
        ActivityLogHelper::logAcademicSessionActivity('Deleted', $session);
        $session->delete();
        $notification = [
            'message' => 'Session Deleted!!',
            'alert-type' => 'success'
        ];
        return redirect()->route('admin.academic.session')->with($notification);
    }
}
