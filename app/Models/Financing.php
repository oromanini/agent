<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Financing extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function proposal(): HasOne
    {
        return $this->hasOne(Proposal::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function owner(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
