<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminProfile;
use App\Http\Requests\UpdateAdminPassword;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admin = Auth::user();
        return view('admin.profile.profile', compact('admin'));
    }





    /**
     * Update the specified resource in storage.
     */
    public function update(AdminProfile $request, $slug)
    {

        $admin = User::where('slug', $slug)->first();
        if ($request->hasFile('profile_photo')) {
            $old_image = $admin->profile_photo;

            if (!empty($old_image) && file_exists(public_path($old_image))) {
                unlink(public_path($old_image));
            }

            $thumb = $request->file('profile_photo');
            $extension = $thumb->getClientOriginalExtension();
            $profilePhoto = time() . "." . $extension;
            $thumb->move('admin/profile/', $profilePhoto);
            $admin->profile_photo = 'admin/profile/' . $profilePhoto;
        }

        $admin->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'other_name' => $request->other_name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        $notification = [
            'message' => 'Profile updated successfully!',
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);

        // dd($admin);

    }

    public function updatePassword(UpdateAdminPassword $request, $slug){

        $admin = User::where('slug', $slug)->first();
        $current_password = $request->current_password;

        if (Auth::attempt(['email' => $admin->email, 'password' => $current_password])) {
            $admin->update(['password' => bcrypt($request->password)]);
            // Auth::logout();

            $notification = [
               'message' => 'Password updated successfully!',
                'alert-type' =>'success'
            ];

            return redirect()->back()->with($notification);
        } else {
            $notification = [
               'message' => 'Current password is incorrect.',
                'alert-type' => 'error'
            ];

            return redirect()->back()->with($notification);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $admin)
    {
        //
    }
}
