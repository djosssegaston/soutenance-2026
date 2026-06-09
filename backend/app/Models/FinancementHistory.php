<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancementHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'financement_id',
        'action',
        'auteur',
        'details',
    ];

    public function funding()
    {
        return $this->belongsTo(Funding::class, 'financement_id');
    }
}
