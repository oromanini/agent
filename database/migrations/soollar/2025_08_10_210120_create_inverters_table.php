<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvertersTable extends Migration
{
    public function up(): void
    {
        Schema::connection('soollar')->create('inverters', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('brand')->nullable();
            $table->float('price')->nullable();
            $table->string('power')->nullable();
            $table->string('voltage')->nullable();
            $table->string('stock')->nullable();
            $table->string('distribution_center');
            $table->string('category');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inverters');
    }
}
