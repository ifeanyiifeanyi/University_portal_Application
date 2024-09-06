<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    public const TYPE_ADMIN = 1;
    public const TYPE_TEACHER = 2;
    public const TYPE_STUDENT = 3;
    public const TYPE_PARENT = 4;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

public function getUserAccessTypeAttribute(){
    if($this->user_type == self::TYPE_ADMIN){
        return "ADMIN";
    } else if($this->user_type == self::TYPE_TEACHER){
        return "LECTURER";
    }else if($this->user_type == self::TYPE_STUDENT){
        return "STUDENT";
    }else if($this->user_type == self::TYPE_PARENT){
        return "PARENT";
    }
    return null;
}
    public function teacher(){
        return $this->hasOne(Teacher::class);
    }
    public function student(){
        return $this->hasOne(Student::class);
    }
    public function parent(){
        return $this->hasOne(Parents::class);
    }

    public function profileImage(){
        return empty($this->profile_photo) ? asset('no_image.jpg') : asset($this->profile_photo);
    }

    public function fullName(){
        return Str::title($this->first_name.' '.$this->last_name. ' '. $this->other_name ?? '');
    }

    public function getFullNameAttribute(){
        return Str::title($this->first_name.' '.$this->last_name. ' '. $this->other_name ?? '');
    }


    public function admin(){
        return $this->hasOne(Admin::class);
    }


    public function isAdmin()
    {
        return $this->user_type == 1;
    }

    public function isTeacher()
    {
        return $this->user_type == 2;
    }

    public function isStudent()
    {
        return $this->user_type == 3;
    }

    public function isParent()
    {
        return $this->user_type == 4;
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
