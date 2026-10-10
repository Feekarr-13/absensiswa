<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'classroom_id', 'student_number', 'birth_date', 'gender'];

    protected function casts(): array { return ['birth_date' => 'date']; }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function classroom(): BelongsTo { return $this->belongsTo(Classroom::class); }
    public function attendances(): HasMany { return $this->hasMany(Attendance::class); }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class, 'parent_student', 'student_id', 'parent_id')
            ->withPivot('relationship', 'is_verified', 'verified_at')->withTimestamps();
    }
}