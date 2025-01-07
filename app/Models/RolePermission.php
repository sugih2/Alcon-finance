<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    // Tentukan tabel jika namanya berbeda dari nama model plural
    protected $table = 'role_permissions';

    // Tentukan kolom yang bisa diisi secara mass-assignment
    protected $fillable = [
        'menu_permission_id',
        'role_id',
    ];

    // Relasi ke model MenuPermission
    // public function menuPermission()
    // {
    //     return $this->belongsTo(MenuPermission::class);
    // }

    // // Relasi ke model Role
    // public function role()
    // {
    //     return $this->belongsTo(Role::class);
    // }
}
