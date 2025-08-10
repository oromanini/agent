<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCablesTable extends Migration
{
    public function up(): void
    {
        Schema::connection('soollar')->create('cables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('size')->nullable();
            $table->string('type')->nullable();
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
        Schema::dropIfExists('cables');
    }
}
