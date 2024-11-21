<?php


namespace App\Services;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Str;
use App\Mail\TeacherWelcomeMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\TeacherProfileUpdatedMail;
use Illuminate\Support\Facades\Storage;
use App\Notifications\TeacherWelcomeNotification;

class TeacherRegistrationService
{
    public function register(array $data)
    {
        // Generate a random password
        $password = Hash::make('password');

        // Create user
        $user = $this->createUser($data, $password);

        // Create teacher profile
        $teacher = $this->createTeacherProfile($data, $user);

        // Handle profile photo if present
        if (isset($data['profile_photo'])) {
            $this->handleProfilePhoto($data['profile_photo'], $user);
        }

        activity()
            ->performedOn($teacher)
            ->causedBy(auth()->user())
            ->withProperties([
                'created_data' => $data
            ])
            ->log('Teacher profile Created');

        // Send welcome email
        $this->sendWelcomeEmail($user, $password);

        return $user;
    }

    private function sendWelcomeEmail(User $user, string $password): void
    {
        $fullName = trim($user->first_name . ' ' . $user->last_name);

        Mail::send(new TeacherWelcomeMail(
            $fullName,
            $user->email,
            $password
        ));
    }

    private function createUser(array $data, string $password): User
    {
        return User::create([
            'user_type' => User::TYPE_TEACHER,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'other_name' => $data['other_name'] ?? null,
            'username' => mt_rand(000000, 999999),
            'slug' => "SLUG" . mt_rand(0000, 9999),
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => Hash::make($password),
        ]);
    }

    private function createTeacherProfile(array $data, User $user): Teacher
    {
        $teacher = new Teacher([
            'date_of_birth' => $data['date_of_birth'],
            'gender' => $data['gender'],
            'teaching_experience' => $data['teaching_experience'],
            'teacher_type' => $data['teacher_type'],
            'teacher_qualification' => $data['teacher_qualification'],
            'teacher_title' => $data['teacher_title'],
            'office_hours' => $data['office_hours'] ?? null,
            'office_address' => $data['office_address'] ?? null,
            'biography' => $data['biography'] ?? null,
            'certifications' => isset($data['certifications']) ? json_encode($data['certifications']) : null,
            'publications' => isset($data['publications']) ? json_encode($data['publications']) : null,
            'number_of_awards' => $data['number_of_awards'] ?? null,
            'date_of_employment' => $data['date_of_employment'],
            'address' => $data['address'],
            'nationality' => $data['nationality'],
            'level' => $data['level'],
            'employment_id' => $this->generateEmploymentId()
        ]);

        $user->teacher()->save($teacher);

        return $teacher;
    }

    private function handleProfilePhoto($photo, User $user): void
    {
        $path = $photo->store('admin/lecturers/profile', 'public');
        $user->update(['profile_photo' => $path]);
    }

    private function generateEmploymentId(): string
    {
        return "EM-ID-" . str_shuffle(mt_rand(1000000, 9999999)) . "CONSO";
    }




    public function update(Teacher $teacher, array $data): Teacher
    {

        // Handle profile photo update
        if (isset($data['profile_photo'])) {
            $this->updateProfilePhoto($teacher, $data['profile_photo']);
        }

        // Update user details
        $teacher->user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'other_name' => $data['other_name'] ?? null,
            'phone' => $data['phone'],
            'email' => $data['email'],
        ]);

        // Update teacher details
        $teacher->update([
            'date_of_birth' => $data['date_of_birth'],
            'gender' => $data['gender'],
            'teaching_experience' => $data['teaching_experience'],
            'teacher_type' => $data['teacher_type'],
            'teacher_qualification' => $data['teacher_qualification'],
            'teacher_title' => $data['teacher_title'],
            'office_hours' => $data['office_hours'] ?? null,
            'office_address' => $data['office_address'] ?? null,
            'biography' => $data['biography'] ?? null,
            'certifications' => isset($data['certifications']) ? json_encode($data['certifications']) : null,
            'publications' => isset($data['publications']) ? json_encode($data['publications']) : null,
            'number_of_awards' => $data['number_of_awards'] ?? null,
            'date_of_employment' => $data['date_of_employment'],
            'address' => $data['address'],
            'nationality' => $data['nationality'],
            'level' => $data['level'],
        ]);

        // Start logging the update activity
        activity()
            ->performedOn($teacher)
            ->causedBy(auth()->user())
            ->withProperties([
                'old_data' => $teacher->toArray(),
                'updated_data' => $data
            ])
            ->log('Teacher profile updated');


        // Send update notification email
        $this->sendUpdateNotificationEmail($teacher);

        return $teacher;
    }

    private function updateProfilePhoto(Teacher $teacher, $photo): void
    {
        // Delete old profile photo if exists
        if ($teacher->user->profile_photo) {
            Storage::disk('public')->delete($teacher->user->profile_photo);
        }

        // Store new profile photo
        $path = $photo->store('admin/lecturers/profile', 'public');
        $teacher->user->update(['profile_photo' => $path]);
    }

    private function sendUpdateNotificationEmail(Teacher $teacher): void
    {
        $updatedBy = auth()->user()->full_name ?? 'Administrator';

        Mail::to($teacher->user->email)->send(
            new TeacherProfileUpdatedMail(
                $teacher->user->first_name,
                $updatedBy
            )
        );
    }
}
