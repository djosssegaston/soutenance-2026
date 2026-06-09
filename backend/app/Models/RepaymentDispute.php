<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepaymentDispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'repayment_id',
        'institution_id',
        'porteur_id',
        'motif',
        'statut',
        'preuves',
    ];

    public function repayment()
    {
        return $this->belongsTo(Repayment::class);
    }
}
