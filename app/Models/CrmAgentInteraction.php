<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmAgentInteraction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmAgentLead::class, 'crm_agent_lead_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
