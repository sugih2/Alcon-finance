<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingShift extends Model
{
    use HasFactory;
    protected $fillable = [
        'kode',
        'jenis',
        'jam_masuk',
        'jam_pulang',
        'awal_masuk',
        'maks_late',
    ];
}
