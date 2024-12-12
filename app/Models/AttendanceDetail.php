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
        'deduction_reason',
    ];

    public function payrollHistoryDetail()
    {
        return $this->belongsTo(PayrollHistoryDetail::class, 'id_payroll_history_detail', 'id');
    }
}
