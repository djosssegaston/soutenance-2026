<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'prenom',
        'sexe',
        'date_naissance',
        'email',
        'password',
        'role',
        'porteur_type',
        'telephone',
        'statut',
        'avatar_url',
        'adresse',
        'pays',
        'departement',
        'commune',
        'arrondissement',
        'quartier',
        'pays_id',
        'departement_id',
        'commune_id',
        'arrondissement_id',
        'quartier_id',
        'activite',
        'entreprise_nom',
        'entreprise_secteur',
        'provider',
        'provider_id',
        'two_factor_secret',
        'two_factor_backup_codes',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'telephone_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function institution()
    {
        return $this->hasOne(Institution::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function notifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    public function kycDocument()
    {
        return $this->hasOne(KycDocument::class);
    }

    public function paysRelation(): BelongsTo
    {
        return $this->belongsTo(Pay::class, 'pays_id');
    }

    public function departementRelation(): BelongsTo
    {
        return $this->belongsTo(Departement::class, 'departement_id');
    }

    public function communeRelation(): BelongsTo
    {
        return $this->belongsTo(Commune::class, 'commune_id');
    }

    public function arrondissementRelation(): BelongsTo
    {
        return $this->belongsTo(Arrondissement::class, 'arrondissement_id');
    }

    public function quartierRelation(): BelongsTo
    {
        return $this->belongsTo(Quartier::class, 'quartier_id');
    }
}
