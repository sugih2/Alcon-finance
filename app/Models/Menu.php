<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'url',
        'parent_id',
        'urutan',
        'icon',
        'status'
    ];
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }

    // Relasi ke menu parent
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    // Relasi ke MenuPermission
    public function menuPermissions()
    {
        return $this->hasMany(MenuPermission::class);
    }
}
