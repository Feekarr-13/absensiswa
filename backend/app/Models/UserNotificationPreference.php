<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationPreference extends Model
{
    protected $fillable = [
        'user_id', 'attendance', 'schedule_reminder', 'announcement', 'correction',
        'email_enabled', 'language', 'dark_mode',
    ];

    protected function casts(): array
    {
        return [
            'attendance' => 'boolean', 'schedule_reminder' => 'boolean', 'announcement' => 'boolean',
            'correction' => 'boolean', 'email_enabled' => 'boolean', 'dark_mode' => 'boolean',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}