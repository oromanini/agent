<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionalKitsTable extends Migration
{
    public function up(): void
    {
        Schema::create('promotional_kits', function (Blueprint $table) {
            $table->id();
            $table->string('panel_brand');
            $table->string('power_brand');
            $table->string('inverter_brand');
            $table->string('kwp');
            $table->string('final_value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotional_kits');
    }
}
