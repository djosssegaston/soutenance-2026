<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pay extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'code', 'indicatif', 'actif'];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
        ];
    }

    public function departements(): HasMany
    {
        return $this->hasMany(Departement::class, 'pays_id');
    }
}
