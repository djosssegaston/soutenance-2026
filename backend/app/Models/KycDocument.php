<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type_piece',
        'numero_piece',
        'date_expiration',
        'recto_path',
        'verso_path',
        'selfie_path',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'date_expiration' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
