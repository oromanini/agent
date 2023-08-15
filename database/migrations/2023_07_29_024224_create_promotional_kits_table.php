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
            $table->float('panel_power');
            $table->string('inverter_brand');
            $table->float('kwp');
            $table->decimal('final_value',10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotional_kits');
    }
}
