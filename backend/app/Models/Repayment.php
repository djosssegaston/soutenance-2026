<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    use HasFactory;

    protected $table = 'remboursements';

    protected $fillable = [
        'project_id',
        'financement_id',
        'institution_id',
        'montant_total',
        'montant_rembourse',
        'montant_restant',
        'date_echeance',
        'date_paiement',
        'statut',
        'niveau_risque',
        'penalites',
        'methode_paiement',
        'transaction_reference',
        'preuve_path',
        'commentaires',
    ];

    protected $casts = [
        'montant_total' => 'decimal:2',
        'montant_rembourse' => 'decimal:2',
        'montant_restant' => 'decimal:2',
        'penalites' => 'decimal:2',
        'date_echeance' => 'date',
        'date_paiement' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function institution()
    {
        return $this->belongsTo(\App\Models\Institution::class);
    }

    public function funding()
    {
        return $this->belongsTo(Funding::class, 'financement_id');
    }

    public function events()
    {
        return $this->hasMany(RepaymentEvent::class, 'repayment_id');
    }

    public function disputes()
    {
        return $this->hasMany(RepaymentDispute::class, 'repayment_id');
    }
}
