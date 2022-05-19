<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('street');
            $table->string('number');
            $table->string('complement')->nullable();
            $table->string('zipcode');
            $table->string('neighborhood');
            $table->bigInteger('city_id')->unsigned();
            $table->bigInteger('client_id')->unsigned();
            $table->bigInteger('consumer_unit_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('consumer_unit_id')->references('id')->on('consumer_units');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
