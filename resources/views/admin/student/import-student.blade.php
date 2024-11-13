@extends('admin.layouts.admin')

@section('title', 'Verify Imported Students')
@section('css')
    <style>
        /* Make table more readable */
        .table {
            font-size: 14px;
        }

        /* Fixed column widths */
        .table th {
            white-space: nowrap;
            background-color: #f8f9fa;
            vertical-align: middle;
            padding: 12px 8px;
        }

        /* Style form controls */
        .form-control {
            padding: 4px 8px;
            height: 32px;
            font-size: 14px;
        }

        /* Set specific widths for different types of fields */
        .field-sm {
            width: 100px;
            min-width: 100px;
        }

        .field-md {
            width: 150px;
            min-width: 150px;
        }

        .field-lg {
            width: 200px;
            min-width: 200px;
        }

        /* Delete button styling */
        .delete-row {
            color: #dc3545;
            cursor: pointer;
            transition: color 0.2s;
            font-size: 16px;
            padding: 4px 8px;
        }

        .delete-row:hover {
            color: #bd2130;
        }

        /* Ensure table header stays visible */
        .table-responsive {
            max-height: 80vh;
        }

        .table thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        /* Zebra striping for better row distinction */
        .table tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, .02);
        }

        /* Hover effect on rows */
        .table tbody tr:hover {
            background-color: rgba(0, 0, 0, .05);
        }

        /* Better input focus styling */
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        /* Column specific styling */
        td {
            vertical-align: middle !important;
            padding: 8px !important;
        }

        .sn-column {
            width: 50px;
            text-align: center;
        }

        .action-column {
            width: 60px;
            text-align: center;
        }
    </style>
@endsection

@section('admin')
    @include('admin.alert')

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.students.import.process') }}" method="POST" id="importForm">
                @csrf
                <input type="hidden" name="department_id" value="{{ $department_id }}">
                <input type="hidden" name="filePath" value="{{ $filePath }}">
                <div class="table-responsive">
                    <table class="table table-bordered" id="studentsTable">
                        <thead>
                            <tr>
                                <th class="sn-column">SN</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Other Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Date of Birth</th>
                                <th>Gender</th>
                                <th>State of Origin</th>
                                <th>Nationality</th>
                                <th>Year of Admission</th>
                                <th>Mode of Entry</th>
                                <th>Current Level</th>
                                <th>JAMB No.</th>
                                <th class="action-column">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                <tr data-row-index="{{ $loop->index }}">
                                    <td class="row-number">{{ $loop->iteration }}</td>
                                    <td>
                                        <input type="text" name="students[{{ $loop->index }}][first_name]"
                                            value="{{ $student['first_name'] }}" class="form-control field-md">
                                    </td>
                                    <td>
                                        <input type="text" name="students[{{ $loop->index }}][last_name]"
                                            value="{{ $student['last_name'] }}" class="form-control field-md">
                                    </td>
                                    <td>
                                        <input type="text" name="students[{{ $loop->index }}][other_name]"
                                            value="{{ $student['other_name'] }}" class="form-control field-md">
                                    </td>
                                    <td>
                                        <input type="email" name="students[{{ $loop->index }}][email]"
                                            value="{{ $student['email'] }}" class="form-control field-lg">
                                    </td>
                                    <td>
                                        <input type="text" name="students[{{ $loop->index }}][phone]"
                                            value="{{ $student['phone_number'] }}" class="form-control field-md">
                                    </td>
                                    <td>
                                        <input type="date" name="students[{{ $loop->index }}][date_of_birth]"
                                            value="{{ $student['date_of_birth_yyyy_mm_dd'] }}"
                                            class="form-control field-md">
                                    </td>
                                    <td>
                                        <select name="students[{{ $loop->index }}][gender]" class="form-control field-sm">
                                            <option value="Male"
                                                {{ $student['gender_malefemaleother'] == 'Male' ? 'selected' : '' }}>Male
                                            </option>
                                            <option value="Female"
                                                {{ $student['gender_malefemaleother'] == 'Female' ? 'selected' : '' }}>
                                                Female
                                            </option>
                                            <option value="Other"
                                                {{ $student['gender_malefemaleother'] == 'Other' ? 'selected' : '' }}>Other
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="students[{{ $loop->index }}][state_of_origin]"
                                            value="{{ $student['state_of_origin'] }}" class="form-control field-md">
                                    </td>
                                    <td>
                                        <input type="text" name="students[{{ $loop->index }}][nationality]"
                                            value="{{ $student['nationality'] }}" class="form-control field-md">
                                    </td>
                                    <td>
                                        <input type="number" name="students[{{ $loop->index }}][year_of_admission]"
                                            value="{{ $student['year_of_admission_yyyy'] }}" class="form-control field-sm">
                                    </td>
                                    <td>
                                        <select name="students[{{ $loop->index }}][mode_of_entry]"
                                            class="form-control field-md">
                                            <option value="UTME"
                                                {{ $student['mode_of_entry_utmedirect_entrytransfer'] == 'UTME' ? 'selected' : '' }}>
                                                UTME
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
                                            value="{{ $student['current_level'] }}" class="form-control field-sm">
                                    </td>
                                    <td>
                                        <input type="text"
                                            name="students[{{ $loop->index }}][jamb_registration_number]"
                                            value="{{ $student['jamb_registration_number'] }}"
                                            class="form-control field-md">
                                    </td>
                                    <td class="action-column">
                                        <i class="fas fa-trash delete-row" title="Remove this student"></i>
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
@endsection

<script>
    // document.addEventListener('DOMContentLoaded', function() {
    //     const table = document.getElementById('studentsTable');

    //     // Function to update row numbers
    //     function updateRowNumbers() {
    //         const rows = table.querySelectorAll('tbody tr');
    //         rows.forEach((row, index) => {
    //             row.querySelector('.row-number').textContent = index + 1;
    //         });
    //     }

    //     // Function to update input names after row removal
    //     function updateInputNames() {
    //         const rows = table.querySelectorAll('tbody tr');
    //         rows.forEach((row, index) => {
    //             const inputs = row.querySelectorAll('input, select');
    //             inputs.forEach(input => {
    //                 const name = input.getAttribute('name');
    //                 if (name) {
    //                     input.setAttribute('name', name.replace(/\[\d+\]/, `[${index}]`));
    //                 }
    //             });
    //             row.setAttribute('data-row-index', index);
    //         });
    //     }

    //     // Delete row functionality
    //     table.addEventListener('click', function(e) {
    //         if (e.target.classList.contains('delete-row')) {
    //             if (confirm('Are you sure you want to remove this student from the import?')) {
    //                 const row = e.target.closest('tr');
    //                 row.remove();
    //                 updateRowNumbers();
    //                 updateInputNames();
    //             }
    //         }
    //     });

    //     // Form submission handler
    //     document.getElementById('importForm').addEventListener('submit', function(e) {
    //         const rows = table.querySelectorAll('tbody tr');
    //         if (rows.length === 0) {
    //             e.preventDefault();
    //             alert('Cannot submit empty import. Please add at least one student.');
    //             return false;
    //         }
    //     });
    // });


    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('studentsTable');
        const tbody = table.querySelector('tbody');

        // Function to update row numbers
        function updateRowNumbers() {
            const rows = tbody.querySelectorAll('tr');
            rows.forEach((row, index) => {
                const rowNumberCell = row.querySelector('.row-number');
                if (rowNumberCell) {
                    rowNumberCell.textContent = index + 1;
                }
            });
        }

        // Function to update input names after row removal
        function updateInputNames() {
            const rows = tbody.querySelectorAll('tr');
            rows.forEach((row, index) => {
                const inputs = row.querySelectorAll('input, select');
                inputs.forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/students\[\d+\]/, `students[${index}]`);
                        input.setAttribute('name', newName);
                    }
                });
                row.setAttribute('data-row-index', index);
            });
        }

        // Delete row functionality with debugging
        tbody.addEventListener('click', function(e) {
            const deleteButton = e.target.closest('.delete-row');
            if (deleteButton) {
                console.log('Delete button clicked');

                if (confirm('Are you sure you want to remove this student from the import?')) {
                    const row = deleteButton.closest('tr');
                    console.log('Row to be deleted:', row);

                    if (row) {
                        try {
                            row.remove();
                            console.log('Row removed successfully');
                            updateRowNumbers();
                            updateInputNames();
                        } catch (error) {
                            console.error('Error removing row:', error);
                        }
                    } else {
                        console.error('Could not find parent row');
                    }
                }
            }
        });

        // Form submission handler
        const importForm = document.getElementById('importForm');
        if (importForm) {
            importForm.addEventListener('submit', function(e) {
                const rows = tbody.querySelectorAll('tr');
                if (rows.length === 0) {
                    e.preventDefault();
                    alert('Cannot submit empty import. Please add at least one student.');
                    return false;
                }
            });
        }

        // Add test function to verify event binding
        function testDeleteFunctionality() {
            const deleteButtons = tbody.querySelectorAll('.delete-row');
            console.log('Found delete buttons:', deleteButtons.length);
            deleteButtons.forEach((btn, index) => {
                console.log(`Delete button ${index + 1} is properly rendered`);
            });
        }

        // Run test on load
        testDeleteFunctionality();
    });
</script>
