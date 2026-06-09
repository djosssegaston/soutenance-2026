<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nom',
        'sigle',
        'type_institution',
        'rccm',
        'ifu',
        'date_creation',
        'agrement',
        'secteur',
        'email',
        'telephone',
        'telephone_secondaire',
        'adresse',
        'bp',
        'statut',
        'description',
        'site_web',
        'logo',
        'pays_id',
        'departement_id',
        'commune_id',
        'arrondissement_id',
        'arrondissement_nom',
        'quartier_id',
        'quartier_nom',
        'resp_prenom',
        'resp_nom',
        'resp_fonction',
        'resp_sexe',
        'resp_date_naissance',
        'resp_nationalite',
        'resp_type_piece',
        'resp_numero_piece',
        'resp_telephone',
        'resp_email',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function analyses()
    {
        return $this->hasMany(InstitutionAnalysis::class);
    }

    public function financements()
    {
        return $this->hasMany(Funding::class);
    }
}
