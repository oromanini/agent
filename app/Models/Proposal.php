<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected array $data = [
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

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function financing(): BelongsTo
    {
        return $this->belongsTo(Financing::class);
    }



    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function homologation(): HasOne
    {
        return $this->hasOne(Homologation::class);
    }

    public function installation(): HasOne
    {
        return $this->hasOne(Installation::class);
    }

    public function kit(): array
    {
        $kit = Kit::where('distributor_code', $this->kit_uuid)->first();

        if ($this->is_manual) {
            $params = json_decode($this->manual_data, true);

            return [
                'inverter_specs' => [
                    'brand' => $params['inverter_brand'],
                    'model' => $params['inverter_model'],
                    'power' => $params['inverter_power'],
                    'warranty' => $params['inverter_warranty'],
                    'quantity' => $params['inverter_quantity'],
                ],
                'panel_specs' => [
                    'brand' => $params['panel_brand'],
                    'model' => $params['panel_model'],
                    'power' => $params['panel_power'],
                    'warranty' => $params['panel_warranty'],
                ],
                'components' => jsonToArray($this->components),
            ];
        }

        return [
            'inverter_specs' => jsonToArray($kit->inverter_specs),
            'panel_specs' => jsonToArray($kit->panel_specs),
            'components' => jsonToArray($kit->components),
        ];
    }
}
