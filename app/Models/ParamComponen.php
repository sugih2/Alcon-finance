<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParamComponen extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'id_regency', 'id_position', 'componen', 'type', 'category', 'amount', 'status'];

    public function regency()
    {
        return $this->belongsTo(Regency::class, 'id_regency');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'id_position');
    }
}
