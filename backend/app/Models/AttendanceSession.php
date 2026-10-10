<?php

namespace App\Models;

use App\Enums\SessionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceSession extends Model
{
    protected $fillable = [
        'schedule_id', 'created_by', 'session_date', 'status', 'started_at', 'late_after',
        'closed_at', 'qr_token_hash', 'previous_qr_token_hash', 'qr_rotated_at', 'qr_rotation_seconds',
    ];

    protected $hidden = ['qr_token_hash', 'previous_qr_token_hash'];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'status' => SessionStatus::class,
            'started_at' => 'datetime',
            'late_after' => 'datetime',
            'closed_at' => 'datetime',
            'qr_rotated_at' => 'datetime',
        ];
    }

    public function schedule(): BelongsTo { return $this->belongsTo(Schedule::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function attendances(): HasMany { return $this->hasMany(Attendance::class); }
}