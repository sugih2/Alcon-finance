<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'fk_parposition',
        'parent_id',
    ];

    public function paramposition()
    {
        return $this->belongsTo(ParamPosition::class, 'fk_parposition');
    }

    public function parent()
    {
        return $this->belongsTo(Position::class, 'parent_id');
    }
}
