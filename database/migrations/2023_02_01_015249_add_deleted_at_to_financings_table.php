<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToFinancingsTable extends Migration
{
    public function up(): void
    {
        Schema::table('financings', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('financings', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
