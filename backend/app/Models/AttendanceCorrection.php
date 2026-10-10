<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceCorrection extends Model
{
    protected $fillable = [
        'attendance_id', 'requested_by', 'previous_status', 'requested_status', 'reason',
        'status', 'decided_by', 'decided_at', 'decision_note',
    ];

    protected function casts(): array
    {
        return [
            'previous_status' => AttendanceStatus::class,
            'requested_status' => AttendanceStatus::class,
            'decided_at' => 'datetime',
        ];
    }

    public function attendance(): BelongsTo { return $this->belongsTo(Attendance::class); }
    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function decider(): BelongsTo { return $this->belongsTo(User::class, 'decided_by'); }
}