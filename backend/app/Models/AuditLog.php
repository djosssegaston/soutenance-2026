<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'icon',
        'color',
        'type',
        'ip',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to log an action
     */
    public static function log(int $userId, string $action, string $icon = 'bi-clock', string $color = 'secondary', string $type = 'info'): self
    {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'icon' => $icon,
            'color' => $color,
            'type' => $type,
            'ip' => request()->ip(),
            'date' => now(),
        ]);
    }
}
