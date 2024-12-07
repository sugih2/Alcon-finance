<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollHistoryDetail extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'id_payroll_history', 
        'id_transaksi_payment', 
        'employee_id', 'salary', 
        'allowance', 'deduction', 
        'total_pendapatan', 
        'total_potongan', 'gaji_bruto', 
        'gaji_bersih'
    ];

    public function payrollHistory()
    {
        return $this->belongsTo(PayrollHistory::class, 'id_payroll_history');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
