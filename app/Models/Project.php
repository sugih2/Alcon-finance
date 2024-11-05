<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'jenis',
        'description',
        'regency_id',
    ];

    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }
}
