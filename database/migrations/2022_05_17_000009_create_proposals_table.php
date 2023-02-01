<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->enum('type', ['normal', 'demand']);
            $table->float('estimated_generation');
            $table->float('average_consumption');
            $table->enum('tension_pattern', ['MONO-220', 'BI-220', 'TRI-220', 'TRI-380']);
            $table->integer('roof_structure');
            $table->integer('number_of_panels');
            $table->float('kw_price', 3, 2);
            $table->dateTime('send_date')->nullable();
            $table->json('components');

            $table->bigInteger('client_id')->unsigned();
            $table->bigInteger('agent_id')->unsigned();
            $table->uuid('kit_uuid');
            $table->bigInteger('pre_inspection_id')->unsigned();
            $table->bigInteger('value_history_id')->unsigned();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('agent_id')->references('id')->on('users');
            $table->foreign('pre_inspection_id')->references('id')->on('pre_inspections');
            $table->foreign('value_history_id')->references('id')->on('proposal_value_histories');

            $table->softDeletes();
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
        Schema::dropIfExists('proposals');
    }
}
