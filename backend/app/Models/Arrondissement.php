<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Arrondissement extends Model
{
    use HasFactory;

    protected $fillable = ['commune_id', 'nom'];

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    public function quartiers(): HasMany
    {
        return $this->hasMany(Quartier::class, 'arrondissement_id');
    }
}
