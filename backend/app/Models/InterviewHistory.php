<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'interview_id',
        'action',
        'auteur',
        'details',
    ];

    public function interview()
    {
        return $this->belongsTo(Interview::class);
    }
}
