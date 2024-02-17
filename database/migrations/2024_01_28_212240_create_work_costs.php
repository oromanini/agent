<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkCosts extends Migration
{
    public function up(): void
    {
        Schema::create('work_costs', function (Blueprint $table) {
            $table->id();
            $table->integer('classification')->unique();
            $table->json('costs');
            $table->json('change_history');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_costs');
    }
}
