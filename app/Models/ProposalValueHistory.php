<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProposalValueHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'commission' => 'array',
        'cash_initial_price' => 'decimal:2',
        'card_initial_price' => 'decimal:2',
        'card_final_price' => 'decimal:2',
        'cash_final_price' => 'decimal:2',
    ];

    public function proposal(): HasOne
    {
        return $this->hasOne(Proposal::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function commissionPercentage(): array
    {
        return jsonToArray($this->commission);
    }
}
