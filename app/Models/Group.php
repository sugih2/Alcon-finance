<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'project_id',
        'leader_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function leader()
    {
        return $this->belongsTo(Employee::class, 'leader_id');
    }
}
