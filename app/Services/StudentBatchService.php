<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Student;
use App\Models\Department;
use Illuminate\Support\Str;
use App\Jobs\SendWelcomeEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StudentBatchService
{
    public function processStudents(array $students, $departmentId)
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'last_processed_index' => null,
            'validation_errors' => []
        ];

        // First, validate the entire structure
        if (!is_array($students)) {
            throw new Exception('Invalid data structure: students must be an array');
        }

        // Pre-validate each record before processing
        foreach ($students as $index => $studentData) {
            try {
                $this->validateSingleStudent($studentData, $index);
            } catch (ValidationException $e) {
                $results['validation_errors'][] = [
                    'row' => $index + 1,
                    'errors' => $e->errors()
                ];
                Log::warning('Validation failed for student record', [
                    'row' => $index + 1,
                    'errors' => $e->errors(),
                    'data' => array_keys($studentData) // Log only keys to avoid sensitive data exposure
                ]);
            }
        }

        // If there are validation errors, return early
        if (!empty($results['validation_errors'])) {
            return $results;
        }

        foreach ($students as $index => $studentData) {
            try {
                // Log the start of processing for each student
                Log::info('Starting to process student record', [
                    'row' => $index + 1,
                    'email' => $studentData['email'] ?? 'no_email_provided'
                ]);

                // Verify data completeness
                $this->verifyDataCompleteness($studentData);

                DB::beginTransaction();

                // Create user account
                $user = User::create([
                    'user_type' => User::TYPE_STUDENT,
                    'first_name' => trim($studentData['first_name']),
                    'last_name' => trim($studentData['last_name']),
                    'other_name' => isset($studentData['other_name']) ? trim($studentData['other_name']) : null,
                    'email' => trim($studentData['email']),
                    'phone' => isset($studentData['phone']) ? trim($studentData['phone']) : null,
                    'username' => mt_rand(1000, 9999),
                    'slug' => 'SLUG'.mt_rand(1000, 9999),
                    'email_verified_at' => now(),
                    'password' => Hash::make('12345678'),
                ]);

                // Generate matric number
                $matNumber = $this->generateMatricNumber();

                // Create student record
                $student = Student::create([
                    'user_id' => $user->id,
                    'department_id' => $departmentId,
                    'matric_number' => $matNumber,
                    'date_of_birth' => Carbon::parse($studentData['date_of_birth'])->format('Y-m-d'),
                    'gender' => trim($studentData['gender']),
                    'state_of_origin' => trim($studentData['state_of_origin']),
                    'nationality' => trim($studentData['nationality']),
                    'year_of_admission' => trim($studentData['year_of_admission']),
                    'mode_of_entry' => trim($studentData['mode_of_entry']) ?? 'UTME',
                    'current_level' => trim($studentData['current_level']) ?? 100,
                    'jamb_registration_number' => $studentData['jamb_registration_number'] ?? null,
                ]);

                DB::commit();

                // Queue welcome email
                SendWelcomeEmail::dispatch($user, $student)->delay(now()->addMinutes(5));

                $results['success']++;
                $results['last_processed_index'] = $index;

            } catch (Exception $e) {
                DB::rollBack();

                $results['failed']++;
                $results['errors'][] = [
                    'row' => $index + 1,
                    'error' => $e->getMessage(),
                    'columns_present' => array_keys($studentData)
                ];

                Log::error('Failed to create student record', [
                    'row' => $index + 1,
                    'error' => $e->getMessage(),
                    'columns_present' => array_keys($studentData),
                    'stack_trace' => $e->getTraceAsString()
                ]);
            }
        }

        return $results;
    }

    private function validateSingleStudent(array $studentData, int $index)
    {
        $validator = Validator::make($studentData, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'state_of_origin' => 'required|string',
            'nationality' => 'required|string',
            'year_of_admission' => 'required|digits:4',
            'mode_of_entry' => 'nullable|in:UTME,Direct Entry,Transfer',
            'current_level' => 'nullable'
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }
    }

    private function verifyDataCompleteness(array $studentData)
    {
        $requiredFields = [
            'first_name', 'last_name', 'email', 'date_of_birth',
            'gender', 'state_of_origin', 'nationality',
            'year_of_admission', 'current_level'
        ];

        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (!isset($studentData[$field]) || trim($studentData[$field]) === '') {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            throw new Exception('Missing required fields: ' . implode(', ', $missingFields));
        }
    }


    public function generateMatricNumber()
    {
        $prefix = 'CONSCO';
        $year = date('y');
        $number = mt_rand(1000, 9999);

        // Format: CONSCO/YY/XXXX
        return sprintf("%s/%s/%04d", $prefix, $year, $number);
    }
}
