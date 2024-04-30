<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('name');
            $table->string('status');
            $table->string('phone_number');
            $table->integer('roof_structure');
            $table->integer('average_consumption');
            $table->decimal('kwh_price');
            $table->integer('tension_pattern');
            $table->json('pricing_data');
            $table->json('kit_data');
            $table->json('discount_data');
            $table->json('manual_data');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('pre_inspection_id')->unsigned()->nullable();
            $table->bigInteger('city_id')->unsigned();
            $table->dateTime('send_date')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('pre_inspection_id')->references('id')->on('pre_inspections');
            $table->foreign('city_id')->references('id')->on('cities');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
}
