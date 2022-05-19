<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolarIncidencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solar_incidences', function (Blueprint $table) {
            $table->id();
            $table->string('latitude');
            $table->string('longitude');
            $table->string('average');
            $table->string('jan');
            $table->string('feb');
            $table->string('mar');
            $table->string('apr');
            $table->string('may');
            $table->string('jun');
            $table->string('jul');
            $table->string('aug');
            $table->string('sep');
            $table->string('oct');
            $table->string('nov');
            $table->string('dec');
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
        Schema::dropIfExists('solar_incidences');
    }
}
