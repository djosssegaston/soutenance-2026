<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalysisHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'analysis_id',
        'action',
        'auteur',
        'details',
    ];

    public function analysis()
    {
        return $this->belongsTo(InstitutionAnalysis::class, 'analysis_id');
    }
}
