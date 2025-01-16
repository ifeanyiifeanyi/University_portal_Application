<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Student;
use App\Models\StudentEmail;
use Illuminate\Http\Request;
use App\Jobs\SendBulkEmailJob;
use App\Mail\StudentCustomEmail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AdminSendStudentEmailController extends Controller
{



    public function showSingleEmailForm(Student $student)
    {
        // dd($student);
        return view('admin.student.send_email.index', compact('student'));
    }

    public function sendSingleEmail(Request $request, Student $student)
    {

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:doc,docx,pdf,jpg,jpeg,png,xls,xlsx,csv'
        ]);

        try {
            // Store attachments
            $storedAttachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $path = Storage::disk('public')->putFile(
                        'email-attachments/' . $student->id,
                        $attachment
                    );
                    $storedAttachments[] = $path;
                }
            }

            // Get admin record for the current user
            $admin = Admin::where('user_id', auth()->id())->firstOrFail();

            // Create email record
            $studentEmail = StudentEmail::create([
                'student_id' => $student->id,
                'sender_id' => auth()->id(),
                'admin_id' => $admin->id,
                'subject' => $request->subject,
                'message' => $request->message,
                'type' => 'single',
                'attachments' => $storedAttachments,
            ]);

            // Send email
            $message = (string) $request->message;

            Mail::to($student->user->email)
                ->queue(new StudentCustomEmail(
                    $student,
                    $request->subject,
                    $message, // Ensure this is a string
                    $storedAttachments,
                    $studentEmail
                ));
            return redirect()->back()->with([
                'alert-type' => 'success',
                'message' => 'Email sent successfully'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }


    /**
     * Get students based on filters
     */

    public function getFilteredStudents(Request $request)
    {
        $query = Student::query()->with('user', 'department');

        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->current_level) {
            // Convert display level to numeric if needed
            $query->where('current_level', $request->current_level);
        }

        return response()->json([
            'students' => $query->get()->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->fullName(),
                    'email' => $student->user->email,
                    'department' => $student->department->name,
                    'level' => $student->department->getDisplayLevel($student->current_level),
                ];
            })
        ]);
    }

    public function showBulkEmailForm()
    {
        $departments = Department::all();
        return view('admin.student.send_email.bulk', compact('departments'));
    }

    public function sendBulkEmail(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'attachments.*' => 'nullable|file|mimes:doc,docx,pdf,jpg,jpeg,png,gif,xls,xlsx,csv|max:10240',
        ]);

        try {
            $students = Student::whereIn('id', $request->student_ids)
                ->with('user')
                ->get();
            // dd($students);


            // Get admin record for the current user
            $admin = Admin::where('user_id', auth()->id())->firstOrFail();

            // Process in chunks to avoid timeout
            foreach ($students->chunk(50) as $chunk) {
                SendBulkEmailJob::dispatch(
                    $chunk,
                    $request->subject,
                    $request->message,
                    $request->file('attachments') ?? [],
                    auth()->id(),
                    $admin->id
                );
            }

            return redirect()->back()->with([
                'alert-type' => 'success',
                'message' => 'Email sent successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to queue bulk email: ' . $e->getMessage());
            return redirect()->back()->with([
                'alert-type' => 'error',
                'message' => 'Failed to queue bulk email: '
            ]);
        }
    }
}
