<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToFinancings extends Migration
{
    public function up(): void
    {
        Schema::table('financings', function (Blueprint $table) {
            $table->bigInteger('status_id')->unsigned()->nullable();
            $table->foreign('status_id')->references('id')->on('statuses');
        });
    }

    public function down(): void
    {
        Schema::table('financings', function (Blueprint $table) {
            //
        });
    }
}
