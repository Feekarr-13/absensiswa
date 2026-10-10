<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Present = 'present';
    case Late = 'late';
    case Sick = 'sick';
    case Permitted = 'permitted';
    case Absent = 'absent';
    case Pending = 'pending';
}