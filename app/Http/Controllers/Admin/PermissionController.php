<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        Permission::create(['name' => $validatedData['name']]);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission created successfully');
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validatedData = $request->validate([
            // 'name' => 'required|unique:permissions,name,' . $permission->id,
            'name' => [
                'required',
                Rule::unique('permissions')->ignore($permission->id)->where(function ($query) use ($request) {
                    return $query->whereRaw('LOWER(name) = ?', [strtolower($request->name)]);
                }),
            ],
        ]);

        $permission->update($validatedData);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully');
    }
}
