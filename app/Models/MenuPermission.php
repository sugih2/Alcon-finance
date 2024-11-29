<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuPermission extends Model
{
    use HasFactory;

    // Tentukan tabel jika namanya berbeda dari nama model plural
    protected $table = 'menu_permissions';

    // Tentukan kolom yang bisa diisi secara mass-assignment
    protected $fillable = [
        'menu_id',
        'c',
        'r',
        'u',
        'd',
    ];

    // Relasi ke model Menu
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    // Relasi ke model RolePermission
    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class);
    }
}
