@extends('admin.layouts.admin')

@section('title', 'Verify Imported Students')
@section('css')

@endsection

@section('admin')
    @include('admin.alert')
    <div class="py-5">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.students.import.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="department_id" value="{{ $department_id }}">
                        <input type="hidden" name="filePath" value="{{ $filePath }}">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Date of Birth</th>
                                        <th>Gender</th>
                                        <th>State of Origin</th>
                                        <th>Nationality</th>
                                        <th>Year of Admission</th>
                                        <th>Mode of Entry</th>
                                        <th>Current Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @dd($students)
                                    @foreach ($students as $student)
                                        <tr>
                                            <td>
                                                <input type="text" name="students[{{ $loop->index }}][first_name]"
                                                    value="{{ $student['first_name'] }}" class="form-control">
                                            </td>
                                            <td>
                                                <input type="text" name="students[{{ $loop->index }}][last_name]"
                                                    value="{{ $student['last_name'] }}" class="form-control">
                                            </td>
                                            <td>
                                                <input type="email" name="students[{{ $loop->index }}][email]"
                                                    value="{{ $student['email'] }}" class="form-control">
                                            </td>
                                            <td>
                                                <input type="date" name="students[{{ $loop->index }}][date_of_birth]"
                                                    value="{{ $student['date_of_birth_yyyy_mm_dd'] }}" class="form-control">
                                            </td>
                                            <td>
                                                <select name="students[{{ $loop->index }}][gender_malefemaleother]" class="form-control">
                                                    <option value="Male"
                                                        {{ $student['gender_malefemaleother'] == 'Male' ? 'selected' : '' }}>Male</option>
                                                    <option value="Female"
                                                        {{ $student['gender_malefemaleother'] == 'Female' ? 'selected' : '' }}>Female
                                                    </option>
                                                    <option value="Other"
                                                        {{ $student['gender_malefemaleother'] == 'Other' ? 'selected' : '' }}>Other
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="students[{{ $loop->index }}][state_of_origin]"
                                                    value="{{ $student['state_of_origin'] }}" class="form-control">
                                            </td>
                                            <td>
                                                <input type="text" name="students[{{ $loop->index }}][nationality]"
                                                    value="{{ $student['nationality'] }}" class="form-control">
                                            </td>
                                            <td>
                                                <input type="number"
                                                    name="students[{{ $loop->index }}][year_of_admission]"
                                                    value="{{ $student['year_of_admission_yyyy'] }}" class="form-control">
                                            </td>
                                            <td>
                                                <select name="students[{{ $loop->index }}][mode_of_entry_utmedirect_entrytransfer]"
                                                    class="form-control">
                                                    <option value="UTME"
                                                        {{ $student['mode_of_entry_utmedirect_entrytransfer'] == 'UTME' ? 'selected' : '' }}>UTME
                                                    </option>
                                                    <option value="Direct Entry"
                                                        {{ $student['mode_of_entry_utmedirect_entrytransfer'] == 'Direct Entry' ? 'selected' : '' }}>
                                                        Direct Entry</option>
                                                    <option value="Transfer"
                                                        {{ $student['mode_of_entry_utmedirect_entrytransfer'] == 'Transfer' ? 'selected' : '' }}>
                                                        Transfer</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="students[{{ $loop->index }}][current_level]"
                                                    value="{{ $student['current_level'] }}" class="form-control">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block mt-4">Import Students</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
