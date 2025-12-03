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
        Schema::create('parcelamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cartao_id')->constrained('cartoes')->cascadeOnDelete();
            $table->string('descricao');
            $table->decimal('valor_total', 15, 2);
            $table->unsignedInteger('numero_parcelas');
            $table->unsignedInteger('parcela_atual')->default(1);
            $table->string('mes_inicio', 7); // YYYY-MM
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcelamentos');
    }
};
