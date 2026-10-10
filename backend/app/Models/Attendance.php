<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    protected $fillable = [
        'attendance_session_id', 'student_id', 'attendance_date', 'status',
        'check_in_at', 'check_out_at', 'source', 'note', 'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'status' => AttendanceStatus::class,
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
        ];
    }

    public function session(): BelongsTo { return $this->belongsTo(AttendanceSession::class, 'attendance_session_id'); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function corrections(): HasMany { return $this->hasMany(AttendanceCorrection::class); }
}