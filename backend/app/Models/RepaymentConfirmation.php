<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepaymentConfirmation extends Model
{
    use HasFactory;

    protected $table = 'remboursement_confirmations';

    protected $fillable = [
        'remboursement_id',
        'institution_id',
        'date_confirmation',
    ];

    protected $casts = [
        'date_confirmation' => 'datetime',
    ];

    public function repayment()
    {
        return $this->belongsTo(Repayment::class, 'remboursement_id');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
