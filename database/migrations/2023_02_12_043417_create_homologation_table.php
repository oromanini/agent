<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomologationTable extends Migration
{
    public function up(): void
    {
        Schema::create('homologations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('proposal_id')->unsigned();
            $table->dateTime('protocol_approval_date')->nullable();
            $table->string('trt_pay_order')->nullable();
            $table->string('proof_of_bill_payment')->nullable();
            $table->string('access_opinion_form')->nullable();
            $table->string('signed_access_opinion_form')->nullable();
            $table->text('notes')->nullable();
            $table->string('single-line-project')->nullable();
            $table->json('checklist');

            $table->foreign('proposal_id')->references('id')->on('proposals');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homologations');
    }
}
