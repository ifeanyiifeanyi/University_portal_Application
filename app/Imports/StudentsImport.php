<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use App\Models\Department;
use Illuminate\Support\Str;
use App\Jobs\SendWelcomeEmail;
use App\Jobs\ProcessStudentImport;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class StudentsImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, WithValidation
{
    private $department_id;

    public function __construct($department_id)
    {
        $this->department_id = $department_id;
    }

    /**
     * @param array $row
     */
    public function model(array $row)
    {
        $matNumber = $this->generateMatricNumber($this->department_id);
        // Create user account
        $user = User::create([
            'user_type' => User::TYPE_STUDENT,
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'other_name' => $row['other_name'] ?? null,
            'email' => $row['email'],
            'phone' => $row['phone'] ?? null,
            'username' => Str::lower($row['first_name'] . '.' . $row['last_name']),
            'password' => Hash::make($row['date_of_birth']), // Using DOB as initial password
        ]);

        // Create student record
        $student = Student::create([
            'user_id' => $user->id,
            'department_id' => $this->department_id,
            'matric_number' => $matNumber,
            'date_of_birth' => $row['date_of_birth'],
            'gender' => $row['gender'],
            'state_of_origin' => $row['state_of_origin'],
            'nationality' => $row['nationality'],
            'year_of_admission' => $row['year_of_admission'],
            'mode_of_entry' => $row['mode_of_entry'],
            'current_level' => $row['current_level'],
        ]);

        // Dispatch welcome email job
        SendWelcomeEmail::dispatch($user, $student);

        return $student;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'state_of_origin' => 'required|string',
            'nationality' => 'required|string',
            'year_of_admission' => 'required|digits:4',
            'mode_of_entry' => 'required|in:UTME,Direct Entry,Transfer',
            'current_level' => 'required',
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 50;
    }

    private function generateMatricNumber($departmentId)
    {
        $schoolCode = 'SHN'; // School code
        $year = date('y'); // Last two digits of the current year
        $department = Department::findOrFail($departmentId);
        $departmentCode = $department->code;

        // Get the latest student number for this year
        $latestStudent = Student::where('year_of_admission', date('Y'))
            ->latest('matric_number')
            ->first();

        if ($latestStudent) {
            // Extract the last 4 digits and increment
            $lastNumber = intval(substr($latestStudent->matric_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            // If no students yet this year, start from 1
            $newNumber = 1;
        }

        // Generate the matric number
        return sprintf("%s/%s/%s/%04d", $schoolCode, $departmentCode, $year, $newNumber);
    }
}
