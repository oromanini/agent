<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProposalValueHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function proposal(): HasOne
    {
        return $this->hasOne(Proposal::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

}
