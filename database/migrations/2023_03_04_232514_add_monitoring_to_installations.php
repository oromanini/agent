<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMonitoringToInstallations extends Migration
{
    public function up()
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->string('monitoring_login')->after('other_expenses')->nullable();
            $table->string('monitoring_password')->after('monitoring_login')->nullable();

            $table->enum('monitoring_app', [
                'Solarman',
                'Shinephone',
                'ISolarCloud',
                'ChintConnect'
            ])->after('monitoring_password')->nullable();
        });
    }

    public function down()
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->dropColumn('monitoring_login');
            $table->dropColumn('monitoring_password');
            $table->dropColumn('monitoring_app');
        });
    }
}
