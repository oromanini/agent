<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration
{

    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['panel', 'inverter']);
            $table->integer('brand_enum');
            $table->integer('warranty')->nullable();
            $table->integer('linear_warranty')->nullable();
            $table->integer('overload')->nullable();
            $table->string('logo')->nullable();
            $table->string('picture')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
}
