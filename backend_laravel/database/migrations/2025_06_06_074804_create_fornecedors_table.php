<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ( !Schema::hasTable('fornecedores') ) {
            Schema::create('fornecedores', function (Blueprint $table) {
                $table->id();
                $table->string('cnpj', 18);
                $table->string('razao_social', 150);
                $table->string('logradouro', 150);
                $table->string('numero', 20);
                $table->string('cep', 9);
                $table->string('bairro', 150);
                $table->string('cidade', 150);
                $table->string('uf', 2);
                $table->string('pais', 150);

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fornecedores');
    }
};
