<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('crm_agent_leads', function (Blueprint $table) {
            $table->string('solar_experience')->nullable()->after('phone_number');
        });
    }

    public function down(): void
    {
        Schema::table('crm_agent_leads', function (Blueprint $table) {
            $table->dropColumn('solar_experience');
        });
    }
};
