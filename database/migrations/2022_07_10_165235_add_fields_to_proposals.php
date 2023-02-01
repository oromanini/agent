<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToProposals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->bigInteger('contract_id')->unsigned()->nullable();
            $table->bigInteger('financing_id')->unsigned()->nullable();
            $table->bigInteger('inspection_id')->unsigned()->nullable();

            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->foreign('financing_id')->references('id')->on('financings');
            $table->foreign('inspection_id')->references('id')->on('inspections');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proposals', function (Blueprint $table) {
            //
        });
    }
}
