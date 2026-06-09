<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';

    protected $fillable = [
        'user_id',
        'institution_id',
        'project_id',
        'interview_id',
        'from_institution',
        'date_heure',
        'objet',
        'description',
        'lieu',
        'statut',
        'notes_porteur',
        'notes_institution',
    ];

    protected $casts = [
        'date_heure' => 'datetime',
        'from_institution' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }
}
