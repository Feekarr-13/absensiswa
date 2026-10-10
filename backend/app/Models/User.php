<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    // role, is_active, school_id sengaja TIDAK fillable (cegah mass assignment / eskalasi hak akses).
    protected $fillable = ['name', 'email', 'username', 'password', 'phone', 'avatar_path'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'is_active' => 'boolean',
        ];
    }

    public function hasRole(Role ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function student(): HasOne { return $this->hasOne(Student::class); }
    public function teacher(): HasOne { return $this->hasOne(Teacher::class); }
    public function guardian(): HasOne { return $this->hasOne(Guardian::class); }
    public function notificationPreference(): HasOne { return $this->hasOne(UserNotificationPreference::class); }
}