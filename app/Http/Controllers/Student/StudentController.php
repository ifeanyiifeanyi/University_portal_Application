<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentprofileRequest;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Department;
use App\Services\StudentService;
use Illuminate\Support\Facades\Auth;
use WisdomDiala\Countrypkg\Models\Country;
use WisdomDiala\Countrypkg\Models\State;

class StudentController extends Controller
{
    protected $authService;

    /**
     * CLASS
     * instance of our auth service class
     */
    public function __construct(AuthService $authService)
    {

        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login.view');
        }

        $student = Student::with('user')->where('user_id', $this->authService->user()->id)->first();
        $totalfees = Payment::where('student_id', $student->id)
            ->whereIn('status', ['paid', 'partial'])
            ->sum('base_amount');

        $student = Student::where('user_id', $this->authService->user()->id)->first();

        $query = Payment::with([
            'paymentType',
            'academicSession',
            'semester',
            'receipt',
            'invoice'
        ])->where('student_id', $student->id);

        // Add status filtering
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(2);

        // Get distinct statuses for dropdown
        $statuses = Payment::where('student_id', $student->id)
            ->distinct('status')
            ->pluck('status');

        return view('student.dashboard', [
            'student' => $student,
            'totalfees' => $totalfees,
            'payments' => $payments,
            'statuses' => $statuses
        ]);
    }

    public function profile()
    {
        $profile = Student::with('user')->where('user_id', $this->authService->user()->id)->first();
        $currentDepartment = Department::find($profile->department_id);
        $levels = $currentDepartment ? $currentDepartment->levels : [];
        return view('student.profile.profile', [
            'student' => $profile,
            'levels' => $levels,
            'currentDepartment' => $currentDepartment
            // 'getuser'=>$getuser
        ]);
    }

    public function updateprofile(StudentprofileRequest $updatestudentprofile, StudentService $studentservice)
    {
        return $studentservice->updateprofile($updatestudentprofile);
    }

    public function virtualid()
    {
        // $getuser = User::where('id',$this->authService->user()->id)->first();
        $profile = Student::with(['department', 'user'])->where('user_id', $this->authService->user()->id)->first();
        return view('student.profile.virtualid', [
            'student' => $profile,
            // 'getuser'=>$getuser
        ]);
    }

    public function getStudentPaymentDashboard($student_id)
    {
        $student = Student::with('user')->where('user_id', $student_id)->first();
        $payments = Payment::with([
            'paymentMethod',
            'academicSession',
            'semester',
            'paymentType',
            'installments',
            'student'
        ])
            ->where('student_id', $student->id)
            ->orderBy('payment_date', 'desc')
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => floatval($payment->amount),
                    'payment_date' => $payment->payment_date->format('Y-m-d'),
                    'status' => $payment->status,
                    'transaction_reference' => $payment->transaction_reference,
                    'payment_method' => $payment->paymentMethod->name,
                    'is_installment' => $payment->is_installment,
                    'remaining_amount' => floatval($payment->remaining_amount),
                    'next_transaction_amount' => floatval($payment->next_transaction_amount),
                    'installment_status' => $payment->installment_status,
                    'next_installment_date' => $payment->next_installment_date ? $payment->next_installment_date->format('Y-m-d') : null,
                    'academic_session' => $payment->academicSession->name,
                    'semester' => $payment->semester->name,
                    'level' => $payment->student->department->getDisplayLevel($payment->level),
                    'payment_type' => $payment->paymentType->name,
                    'installments' => $payment->is_installment ? $payment->installments->map(function ($installment) {
                        return [
                            'amount' => floatval($installment->amount),
                            'due_date' => $installment->due_date->format('Y-m-d'),
                            'status' => $installment->status,
                            'installment_number' => $installment->installment_number
                        ];
                    }) : null
                ];
            });

        return response()->json([
            'payments' => $payments
        ]);
    }


    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'cropped_image_data' => 'required|string',
        ]);

        $user = User::where('id', $this->authService->user()->id)->first();

        if ($request->has('cropped_image_data') && !empty($request->cropped_image_data)) {
            // Get the old image path
            $old_image = $user->profile_photo;

            // Delete the old image if it exists
            if (!empty($old_image) && file_exists(public_path($old_image))) {
                unlink(public_path($old_image));
            }

            // Process the base64 cropped image data
            $imageData = $request->cropped_image_data;

            // Remove the data URL prefix (e.g., "data:image/jpeg;base64,")
            $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
            $imageData = base64_decode($imageData);

            // Generate unique filename
            $profilePhoto = time() . '_profile.jpg';
            $imagePath = 'admin/students/profile/' . $profilePhoto;

            // Ensure directory exists
            $directory = public_path('admin/students/profile/');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Save the cropped image
            file_put_contents(public_path($imagePath), $imageData);

            // Update user profile photo path
            $user->profile_photo = $imagePath;
            $user->save();

            return redirect()->back()->with('success', 'Profile picture updated successfully');
        }

        return redirect()->back()->with('error', 'No image data received');
    }

    public function editProfile()
    {
        $countries = Country::all();
        $states = State::all();
        $student = Student::with('user')->where('user_id', $this->authService->user()->id)->first();
        $currentDepartment = Department::find($student->department_id);
        $levels = $currentDepartment ? $currentDepartment->levels : [];
        return view('student.profile.edit', [
            'student' => $student,
            'levels' => $levels,
            'currentDepartment' => $currentDepartment,
            'countries' => $countries,
            'states' => $states
        ]);
    }

    public function changepassword()
    {
        return view('student.profile.changepassword');
    }
    public function updatepassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|different:current_password|confirmed'
        ]);
        $user = User::where('id', $this->authService->user()->id)->first();
        $current_password = $request->current_password;

        if (Auth::attempt(['email' => $user->email, 'password' => $current_password])) {
            $user->update(['password' => bcrypt($request->password)]);

            return redirect()->back()->with('success', 'Password changed successfully');
        } else {

            return redirect()->back()->with('error', 'Current password is incorrect.');
        }
    }
}
