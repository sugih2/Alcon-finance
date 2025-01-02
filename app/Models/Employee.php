<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'nip',
        'nik',
        'name',
        'address',
        'phone',
        'email',
        'birth_date',
        'position_id',
        'status',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
    
    public function payrollHistoryDetails()
    {
        return $this->hasMany(PayrollHistoryDetail::class, 'employee_id');
    }
}
