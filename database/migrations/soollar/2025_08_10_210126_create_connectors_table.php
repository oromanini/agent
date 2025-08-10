<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConnectorsTable extends Migration
{
    public function up(): void
    {
        Schema::connection('soollar')->create('connectors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('price')->nullable();
            $table->string('stock')->nullable();
            $table->string('distribution_center');
            $table->string('category');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connectors');
    }
}
