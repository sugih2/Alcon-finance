<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeductionGroup extends Model
{
    use HasFactory;
    protected $table = 'deduction_groups';
    protected $fillable = [
        'id_payroll_history',
        'group_id',
        'amount'
    ];

    public function payrollHistory()
    {
        return $this->belongsTo(PayrollHistory::class, 'id_payroll_history');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
