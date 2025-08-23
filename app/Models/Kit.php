<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function kitSpecs(): array
    {
        return [
            'panel' => json_decode($this->panel_specs, true),
            'inverter' => json_decode($this->inverter_specs, true),
        ];
    }

    public function fillFromAttributes(
        string $description,
        float $kwp,
        float $cost ,
        int $roof_structure,
        int $tension_pattern,
        array $components,
        array $panel_specs,
        array $inverter_specs,
        string $distributor_name,
        string $distributor_code,
        string $availability,
        bool $is_active,
    ): Kit {
        $this->description = $description;
        $this->kwp = $kwp;
        $this->cost = $cost;
        $this->roof_structure = $roof_structure;
        $this->tension_pattern = $tension_pattern;
        $this->components = $components;
        $this->panel_specs = json_encode($panel_specs);
        $this->inverter_specs = json_encode($inverter_specs);
        $this->distributor_name = $distributor_name;
        $this->distributor_code = $distributor_code;
        $this->availability = $availability;
        $this->is_active = $is_active;

        return $this;
    }
}
