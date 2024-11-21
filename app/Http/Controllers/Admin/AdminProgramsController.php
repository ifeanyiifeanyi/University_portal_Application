<?php

namespace App\Http\Controllers\Admin;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUpdateProgramRequest;

class AdminProgramsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programs = Program::all();
        return view('admin.programs.index', compact('programs'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUpdateProgramRequest $request)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validated();

            $program = Program::create($validatedData);

            DB::commit();

            return redirect()
                ->route('admin.programs.index')
                ->with('success', 'Program created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create program: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateUpdateProgramRequest $request, Program $program)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validated();

            $program->update($validatedData);

            DB::commit();

            return redirect()
                ->route('admin.programs.index')
                ->with('success', 'Program updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Failed to update program: ' . $e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        try {
            DB::beginTransaction();

            $program->delete();

            DB::commit();

            return redirect()
                ->route('admin.programs.index')
                ->with('success', 'Program deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Failed to delete program: ' . $e->getMessage());
        }
    }
}
