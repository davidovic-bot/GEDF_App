<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    
    protected $fillable = [
        'utilisateur_id',
        'action',
        'module',
        'description',
        'adresse_ip',
        'user_agent',
        'anciennes_valeurs',
        'nouvelles_valeurs',
        'url'
    ];

    protected $casts = [
        'anciennes_valeurs' => 'array',
        'nouvelles_valeurs' => 'array',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
}