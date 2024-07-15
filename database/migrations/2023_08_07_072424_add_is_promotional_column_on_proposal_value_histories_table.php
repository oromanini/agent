<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPromotionalColumnOnProposalValueHistoriesTable extends Migration
{

    public function up(): void
    {
        Schema::table('proposal_value_histories', function (Blueprint $table) {
            $table->boolean('is_promotional');
        });
    }

    public function down(): void
    {
        Schema::table('proposal_value_histories', function (Blueprint $table) {
            $table->dropColumn('is_promotional');
        });
    }
}
