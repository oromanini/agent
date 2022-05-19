<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreInspection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
