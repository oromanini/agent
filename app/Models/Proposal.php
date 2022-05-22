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

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function preInspection(): BelongsTo
    {
        return $this->belongsTo(PreInspection::class);
    }

    public function valueHistory(): BelongsTo
    {
        return $this->belongsTo(ProposalValueHistory::class);
    }

    public function setRoofOrientationsAttribute()
    {
        return json_decode($this->roof_structure, true);
    }
}
