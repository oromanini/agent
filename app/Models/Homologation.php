<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Homologation extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $guarded = [];
    protected $dates = ['protocol_approval_date'];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();;
    }

    public function secondaryOwner(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();;
    }
}
