<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectValidation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'admin_id',
        'decision',
        'commentaire',
        'date_decision',
    ];

    protected $casts = [
        'date_decision' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
