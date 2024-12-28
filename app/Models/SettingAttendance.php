<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingAttendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'min_minutes',
        'max_minutes',
        'deduction_type',
        'deduction_value',
    ];

}
