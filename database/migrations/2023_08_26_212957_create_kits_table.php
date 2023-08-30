<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKitsTable extends Migration
{
    public function up(): void
    {
        Schema::create('kits', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->float('kwp');
            $table->float('cost');
            $table->integer('roof_structure');
            $table->integer('tension_pattern');
            $table->json('components');
            $table->json('panel_specs');
            $table->json('inverter_specs');
            $table->string('distributor_name');
            $table->string('distributor_code');
            $table->date('availability');
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kits');
    }
}
