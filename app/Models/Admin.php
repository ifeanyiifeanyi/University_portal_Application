<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_SUPER_ADMIN = "superAdmin";
    public const TYPE_ADMIN = "admin";
    public const TYPE_STAFF = 'staff';

    protected $guarded = [];

    public function getAdminUserRoleAttribute(){
        return Str::upper($this->role);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isSuperAdmin()
    {
        return $this->role === 'superAdmin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }
}
