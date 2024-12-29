<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollProcessDetail extends Model
{
    use HasFactory;
    protected $fillable = 
    [
        'id_payroll_history','payment_date', 'payment_amount', 'payment_method', 'payment_status', 'processed_by'
    ];

    public function payrollHistory()
    {
        return $this->belongsTo(PayrollHistory::class, 'id_payroll_history');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
