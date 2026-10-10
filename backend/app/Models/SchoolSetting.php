<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'school_name', 'address', 'logo_path', 'timezone', 'late_after_minutes',
        'qr_rotation_seconds', 'operating_start', 'operating_end',
    ];

    public static function current(): self
    {
        return static::query()->first() ?? new static(['school_name' => 'Sekolah']);
    }
}