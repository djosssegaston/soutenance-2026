<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutionAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'institution_id',
        'analyste_id',
        'statut',
        'risk_score',
        'score_credibilite',
        'score_solvabilite',
        'note_globale',
        'recommandation',
        'commentaire',
        'entretien_date',
    ];

    protected $casts = [
        'entretien_date' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function analyste()
    {
        return $this->belongsTo(User::class, 'analyste_id');
    }

    public function histories()
    {
        return $this->hasMany(AnalysisHistory::class, 'analysis_id');
    }
}
