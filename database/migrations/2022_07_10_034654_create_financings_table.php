<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financings', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['À vista', 'Financiamento', '40+60', 'Cartão', 'Personalizado']);
            $table->string('full_name')->nullable();
            $table->string('owner_document')->nullable();
            $table->string('birthdate')->nullable();
            $table->string('property_situation')->nullable();
            $table->string('income')->nullable();
            $table->string('patrimony')->nullable();
            $table->string('profession')->nullable();
            $table->string('bank')->nullable();
            $table->string('installments')->nullable();
            $table->string('payment_grace')->nullable();
            $table->longText('note')->nullable();

            $table->string('proof_of_income')->nullable();
            $table->string('document_file')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financings');
    }
}
