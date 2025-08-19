<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModuleBrandsTable extends Migration
{
    public function up(): void
    {
        Schema::connection('soollar')->create('module_brands', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->float('logo')->nullable();
            $table->integer('warranty')->default(12);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_brands');
    }
}
