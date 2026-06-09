<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancementDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'financement_id',
        'type_document',
        'fichier',
    ];

    public function funding()
    {
        return $this->belongsTo(Funding::class, 'financement_id');
    }
}
