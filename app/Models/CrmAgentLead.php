<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmAgentLead extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'generated_password' => 'encrypted',
    ];

    public const STATUSES = [
        'novo',
        'em_atendimento',
        'aguardando_resposta',
        'confeccao_de_contrato',
        'contrato_assinado',
        'desistencia',
    ];

    public function interactions(): HasMany
    {
        return $this->hasMany(CrmAgentInteraction::class)->latest();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
