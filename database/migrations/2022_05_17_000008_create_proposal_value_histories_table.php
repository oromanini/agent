<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProposalValueHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposal_value_histories', function (Blueprint $table) {
            $table->id();
            $table->float('kit_cost', 10, 2);
            $table->float('initial_price', 10, 2);
            $table->float('final_price', 10, 2);
            $table->float('commission_percent', 4, 2)->default(10);
            $table->float('discount_percent', 3, 2)->default(0);
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proposal_value_histories');
    }
}
