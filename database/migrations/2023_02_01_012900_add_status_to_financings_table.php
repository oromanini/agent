<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToFinancingsTable extends Migration
{
    public function up()
    {
        Schema::table('financings', function (Blueprint $table) {
            $table->string('status')->default('Aguardando');
        });
    }

    public function down()
    {
        Schema::table('financings', function (Blueprint $table) {
            $table->drop('status');
        });
    }
}
