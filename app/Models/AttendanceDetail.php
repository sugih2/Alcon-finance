<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceDetail extends Model
{
    use HasFactory;
    protected $table = 'attendance_details';
    protected $fillable = [
        'id_payroll_history_detail',
        'tanggal',
        'earnings',
        'deductions',
        'overtime_earnings',
        'overtime_hours',
        'deduction_reason',
    ];

    protected $casts = [
        'earnings' => 'array',
        'deductions' => 'array',
    ];

    public function getEarningsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function getDeductionsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function payrollHistoryDetail()
    {
        return $this->belongsTo(PayrollHistoryDetail::class, 'id_payroll_history_detail', 'id');
    }
}
