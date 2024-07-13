<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOwnerToContracts extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->unsignedBigInteger('secondary_owner_id')->nullable();
            $table->foreign('owner_id')->references('id')->on('users');
            $table->foreign('secondary_owner_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('owner_id');
            $table->dropColumn('secondary_owner_id');
        });
    }
}
