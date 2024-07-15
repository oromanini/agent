<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->text('name');
            $table->enum('type', ['person', 'company']);
            $table->string('document');
            $table->string('owner_document')->nullable();
            $table->string('account_owner_document')->nullable();
            $table->string('birthdate')->nullable();
            $table->string('alias')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number');
            $table->bigInteger('agent_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
}
