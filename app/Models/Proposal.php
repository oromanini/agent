<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $data = [
        'created_at',
        'updated_at',
        'deleted_at',
        'send_date'
    ];

    public function client(): HasOne
    {
        return $this->hasOne(Proposal::class);
    }

    public function agent(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function preInspection(): HasOne
    {
        return $this->hasOne(PreInspection::class);
    }

    public function proposalValueHistory(): BelongsTo
    {
        return $this->belongsTo(ProposalValueHistory::class);
    }
}
