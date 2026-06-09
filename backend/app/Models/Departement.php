<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departement extends Model
{
    use HasFactory;

    protected $fillable = ['pays_id', 'nom', 'code'];

    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pay::class, 'pays_id');
    }

    public function communes(): HasMany
    {
        return $this->hasMany(Commune::class);
    }
}
