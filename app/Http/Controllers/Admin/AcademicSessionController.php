<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\createAcademicSessionRequest;
use App\Models\AcademicSession;
use Illuminate\Http\Request;

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
        AcademicSession::create($validatedData);
        $notification = [
            'message' => 'Session Created!!',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }


    public function edit($id){
        $academicSessions = AcademicSession::simplePaginate(100);
        $academicSessionSingle = AcademicSession::find($id);
        return view('admin.academicSession.index', compact('academicSessionSingle', 'academicSessions'));
    }

    public function update($id, createAcademicSessionRequest $request){
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
        $notification = [
            'message' => 'Session Updated!!',
            'alert-type' => 'success'
        ];
        return redirect()->route('admin.academic.session')->with($notification);
    }

    public function destroy($id){
        $session = AcademicSession::findOrFail($id);
        $session->delete();
        $notification = [
            'message' => 'Session Deleted!!',
            'alert-type' => 'success'
        ];
        return redirect()->route('admin.academic.session')->with($notification);

    }
}
