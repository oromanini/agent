<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdfTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'html',
        'css',
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function activeProposalTemplate(): ?self
    {
        return self::query()
            ->where('type', 'proposal')
            ->where('is_active', true)
            ->latest('updated_at')
            ->first();
    }
}
