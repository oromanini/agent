<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProposalValueHistoriesTable extends Migration
{
    public function up(): void
    {
        Schema::table('proposal_value_histories', function (Blueprint $table) {
            $table->json('commission')->nullable()->after('commission_percent');
            $table->decimal('cash_initial_price', 12, 2)->nullable()->after('initial_price');
            $table->decimal('card_initial_price', 12, 2)->nullable()->after('cash_initial_price');
            $table->decimal('card_final_price', 12, 2)->nullable()->after('final_price');
            $table->decimal('cash_final_price', 12, 2)->nullable()->after('card_final_price');
        });
    }

    public function down(): void
    {
        Schema::dropColumns('proposal_value_histories', [
            'commission',
            'card_final_price',
            'cash_final_price',
        ]);
    }
}
