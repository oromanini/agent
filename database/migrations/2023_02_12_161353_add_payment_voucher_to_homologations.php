<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentVoucherToHomologations extends Migration
{
    public function up(): void
    {
        Schema::table('homologations', function (Blueprint $table) {
            $table->string('payment_voucher')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('homologations', function (Blueprint $table) {
            //
        });
    }
}
