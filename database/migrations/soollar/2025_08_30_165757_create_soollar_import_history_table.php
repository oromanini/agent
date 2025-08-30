<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSoollarImportHistoryTable extends Migration
{
    public function up(): void
    {
        Schema::connection('soollar')->create('soollar_import_history', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->enum('status', ['SUCCESS', 'PROCESSING', 'ERROR']);
            $table->integer('created_products');
            $table->integer('updated_products');
            $table->integer('created_kits');
            $table->integer('updated_kits');
            $table->integer('elapsed_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soollar_import_history');
    }
}
