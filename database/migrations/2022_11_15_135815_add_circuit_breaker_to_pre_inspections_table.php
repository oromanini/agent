<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCircuitBreakerToPreInspectionsTable extends Migration
{
    public function up(): void
    {
        Schema::table('pre_inspections', function (Blueprint $table) {
            $table->string('circuit_breaker_amperage');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
