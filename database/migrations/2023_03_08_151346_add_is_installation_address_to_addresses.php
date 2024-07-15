<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsInstallationAddressToAddresses extends Migration
{
    public function up()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->boolean('is_installation_address')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            //
        });
    }
}
