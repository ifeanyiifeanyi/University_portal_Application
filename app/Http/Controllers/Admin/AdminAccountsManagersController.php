<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AdminAccountsManagersController extends Controller
{


    public function index()
    {
        $admins = Admin::with('user')->whereHas('user')->get();
        // dd($admins);
        return view('admin.administrators.index', compact('admins'));
    }

    public function show(Admin $admin){
        return view('admin.administrators.details', compact('admin'));
    }

    public function create(){
        $roles = config('user_access.admin');
        return view('admin.administrators.create', compact('roles'));
    }

    public function store(Request $request){
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'other_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required',
            'role' => 'required',
            'password' => 'required',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // check if generated username exists
        $baseUsername = Str::lower($request->first_name . '.'.$request->last_name);
        $username = $baseUsername;
        if (User::where('username', $username)->exists()) {
            return back()->with(['message' => 'Username exists, try again later', 'alert-type' => 'error']);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'other_name' => $request->other_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'slug' => Str::slug($request->first_name.$request->last_name),
                'username' => $request->first_name.'.'.$request->last_name,
                'user_type' => User::TYPE_ADMIN,
                'password' => Hash::make($request->password)
            ]);
            if ($request->hasFile('profile_photo')) {
                $profilePhoto = $request->file('profile_photo');
                $extension = $profilePhoto->getClientOriginalExtension();
                $profilePhotoName = time() . "." . $extension;
                $profilePhoto->move('admin/lecturers/profile/', $profilePhotoName);
                $user->profile_photo = 'admin/admin/profile/' . $profilePhotoName;
                $user->save();
            }

            Admin::create([
                'user_id' => $user->id,
                'role' => $request->role
            ]);
            DB::commit();
            return redirect()->route('admin.accounts.managers.view')->with([
                'message' => 'Administrator account created successfully.',
                'alert-type' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['message' => 'An error occurred while creating the administrator account. Please try again.'.$e->getMessage(), 'alert-type' => 'error']);
        }

    }

    public function destroy(Admin $admin){
        DB::beginTransaction();
        try {
            $user = $admin->user;

            $admin->delete();
            $user->delete();
            DB::commit();
            return redirect()->route('admin.accounts.managers.view')->with(['message' => 'Administrator account deleted successfully.', 'alert-type' =>'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['message' => 'An error occurred while deleting the administrator account. Please try again.'.$e->getMessage(), 'alert-type' => 'error']);
        }
    }
}
