<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class AdminUserRoleController extends Controller
{
    public function index()
    {
        $admins = Admin::with('user', 'user.roles')->get();
        $roles = Role::all();
        return view('admin.admin-users.roles', compact('admins', 'roles'));
    }

    public function assignRoles(Request $request)
    {
        $validatedData = $request->validate([
            'admin_id' => 'required|exists:admins,id',
            'roles' => 'array',
        ]);

        $admin = Admin::findOrFail($validatedData['admin_id']);
        $user = $admin->user;

        // Sync roles for the user
        $user->syncRoles($validatedData['roles'] ?? []);

        return redirect()->route('admin.admin-users.roles')
            ->with('success', 'Roles assigned successfully');
    }

    public function revokeRole(Request $request)
    {
        $validatedData = $request->validate([
            'admin_id' => 'required|exists:admins,id',
            'role' => 'required|exists:roles,name',
        ]);

        $admin = Admin::findOrFail($validatedData['admin_id']);
        $user = $admin->user;

        if ($user->hasRole($validatedData['role'])) {
            $user->removeRole($validatedData['role']);
            return redirect()->back()->with('success', 'Role revoked successfully');
        }

        return redirect()->back()->with('error', 'User does not have this role');
    }
}
