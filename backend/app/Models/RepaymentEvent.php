<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepaymentEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'repayment_id',
        'type_evenement',
        'details',
        'auteur',
    ];

    public function repayment()
    {
        return $this->belongsTo(Repayment::class);
    }
}
