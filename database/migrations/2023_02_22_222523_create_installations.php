<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallations extends Migration
{
    public function up(): void
    {
        Schema::create('installations', function (Blueprint $table) {
            $table->id();
            $table->date('installation_forecast')->nullable();
            $table->date('installation_date')->nullable();
            $table->decimal('ca_cost', 10, 2)->nullable();
            $table->text('ca_invoice')->nullable();
            $table->text('ca_proof_of_payment')->nullable();
            $table->decimal('installation_cost', 10, 2)->nullable();
            $table->text('installation_invoice')->nullable();
            $table->text('installation_proof_of_payment')->nullable();
            $table->json('other_expenses')->nullable();
            $table->json('installation_images')->nullable();

            $table->bigInteger('proposal_id')->unsigned();
            $table->bigInteger('status_id')->unsigned();

            $table->foreign('proposal_id')->references('id')->on('proposals');
            $table->foreign('status_id')->references('id')->on('statuses');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installations');
    }
}
