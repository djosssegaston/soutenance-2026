<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRule extends Model
{
    use HasFactory;

    protected $table = 'document_rules';

    protected $fillable = [
        'context',
        'secteur_id',
        'label',
        'slug',
        'obligatoire',
        'acteur',
        'types_mime',
        'max_size',
        'description',
        'is_active',
        'order_column',
    ];

    protected function casts(): array
    {
        return [
            'obligatoire' => 'boolean',
            'is_active' => 'boolean',
            'max_size' => 'integer',
            'order_column' => 'integer',
        ];
    }

    public function secteur()
    {
        return $this->belongsTo(Secteur::class, 'secteur_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForRole($query, string $role)
    {
        return $query->where('acteur', $role);
    }

    public function scopeForContext($query, string $context)
    {
        return $query->where('context', $context);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_column')->orderBy('label');
    }
}
