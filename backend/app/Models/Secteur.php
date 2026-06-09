<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secteur extends Model
{
    use HasFactory;

    protected $table = 'secteurs';

    protected $fillable = [
        'nom',
        'description',
        'icone',
        'is_active',
        'order_column',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order_column' => 'integer',
        ];
    }

    public function documentRules()
    {
        return $this->hasMany(DocumentRule::class, 'secteur_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_column')->orderBy('nom');
    }
}
