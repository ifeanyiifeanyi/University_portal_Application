@extends('admin.layouts.admin')

@section('title', 'Semester Manager')

@section('admin')
    <div class="container card py-3 px-3">

        <h4>Semester Manager</h4>
        <div class="row mb-5">
            <div class="col-md-6">
                <a href="{{ route('semester-manager.create') }}" class="btn btn-secondary"><i class="fas fa-plus"></i> Create New Semester</a>
            </div>
            <div class="col-md-6">
                <form action="{{ route('semester.manager.search') }}" method="GET">
                    <div class="input-group">
                        <input type="search" class="form-control" name="search" placeholder="Search semesters...">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i> Search</button>
                    </div>
                </form>
            </div>
        </div>
        <form action="{{ route('semester.manager.bulk-action') }}" method="POST" onsubmit="return confirm("Are you sure of this action ?")">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <select name="action" class="form-select d-inline-block w-auto mr-2">
                    <option value="delete">Delete Selected</option>
                    <option value="change_session">Change Academic Session</option>
                </select>
                <select name="new_session" class="form-select d-inline-block w-auto mr-2">
                    @foreach ($academicSessions as $session)
                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary"><i class="fas fa-filter"></i> Apply</button>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Name</th>
                        <th>Season</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Academic Session</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($semesters as $semester)
                        <tr>
                            <td>
                                @if ($semester->canBeDeleted())
                                    <input type="checkbox" name="semesters[]" value="{{ $semester->id }}">
                                @else
                                    <input type="checkbox" disabled title="This semester cannot be deleted">
                                @endif
                            </td>
                            <td>
                                {{ $semester->name }}
                                @if ($semester->is_current == true)
                                    <p><span class="bg-success p-1 text-light">Current Semester</span></p>
                                @endif
                            </td>
                            <td>{{ $semester->season }}</td>
                            <td>{{ $semester->start_date }}</td>
                            <td>{{ $semester->end_date }}</td>
                            <td>
                                {{ $semester->academicSession->name }}
                                @if ($semester->academicSession->is_current == true)
                                    <p><span class="bg-success p-1 text-light">Current Session</span></p>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('semester-manager.toggle-current', $semester) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="btn btn-sm {{ $semester->is_current ? 'btn-success' : 'btn-secondary' }}">
                                        {{ $semester->is_current ? 'Current' : 'Set Current' }}
                                    </button>
                                </form>
                                <a href="{{ route('semester-manager.edit', $semester) }}"
                                    class="btn btn-sm text-primary"><i class="fas fa-edit"></i></a>
                                @if ($semester->canBeDeleted())
                                    <form action="{{ route('semester-manager.destroy', $semester) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm text-danger"
                                            onclick="return confirm('Are you sure? This action cannot be undone.')"><i class="fas fa-trash"></i></button>
                                    </form>
                                @else
                                <x-disable-icon />
                                    {{-- <button class="btn btn-sm btn-secondary" disabled
                                        title="This semester cannot be deleted">Delete</button> --}}
                                @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>
@endsection
