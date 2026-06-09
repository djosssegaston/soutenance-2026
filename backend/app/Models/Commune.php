<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commune extends Model
{
    use HasFactory;

    protected $fillable = ['departement_id', 'nom', 'code'];

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }

    public function arrondissements(): HasMany
    {
        return $this->hasMany(Arrondissement::class);
    }
}
