<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Add3dImageToInspections extends Migration
{
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->string('3d_image')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            //
        });
    }
}
