<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
    public function up(): void
    {
        Schema::connection('soollar')->create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('power')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->float('price')->nullable();
            $table->string('distribution_center');
            $table->string('category');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
}
