<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'institution_id',
        'porteur_id',
        'analyste_id',
        'titre',
        'description',
        'type_entretien',
        'date_entretien',
        'heure_entretien',
        'lieu',
        'statut',
        'convocation_pdf',
        'compte_rendu',
        'decision_preliminaire',
    ];

    protected $casts = [
        'date_entretien' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function porteur()
    {
        return $this->belongsTo(User::class, 'porteur_id');
    }

    public function analyste()
    {
        return $this->belongsTo(User::class, 'analyste_id');
    }

    public function histories()
    {
        return $this->hasMany(InterviewHistory::class);
    }
}
