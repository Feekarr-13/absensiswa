<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    use SoftDeletes;

    protected $fillable = ['academic_year_id', 'name', 'grade_level', 'homeroom_teacher_id'];

    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }
    public function homeroomTeacher(): BelongsTo { return $this->belongsTo(Teacher::class, 'homeroom_teacher_id'); }
    public function students(): HasMany { return $this->hasMany(Student::class); }
    public function schedules(): HasMany { return $this->hasMany(Schedule::class); }
}