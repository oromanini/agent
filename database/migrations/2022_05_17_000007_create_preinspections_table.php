<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreInspectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_inspections', function (Blueprint $table) {
            $table->id();
            $table->json('roof')->nullable();
            $table->json('pattern')->nullable();
            $table->json('circuit_breaker')->nullable();
            $table->json('switchboard')->nullable();
            $table->json('post')->nullable();
            $table->json('compass')->nullable();
            $table->json('croqui')->nullable();
            $table->longText('observations')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pre_inspections');
    }
}
