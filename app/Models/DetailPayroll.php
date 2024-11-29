<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPayroll extends Model
{
    use HasFactory;
    protected $fillable = ['id_transaksi', 'id_employee', 'id_component', 'amount'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'id_employee');
    }

    public function component()
    {
        return $this->belongsTo(ParamComponen::class, 'id_component');
    }

    public function masterPayroll()
    {
        return $this->belongsTo(MasterPayroll::class, 'id_transaksi');
    }
}
