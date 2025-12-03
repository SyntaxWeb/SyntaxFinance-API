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
        Schema::create('dividas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('mes', 7); // YYYY-MM
            $table->decimal('valor', 15, 2);
            $table->string('motivo');
            $table->enum('categoria', ['cartao', 'fixa', 'variavel', 'outro']);
            $table->date('data');
            $table->enum('status', ['paga', 'aberta'])->default('aberta');
            $table->foreignId('cartao_id')->nullable()->constrained('cartoes')->nullOnDelete();
            $table->foreignId('parcelamento_id')->nullable()->constrained('parcelamentos')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dividas');
    }
};
