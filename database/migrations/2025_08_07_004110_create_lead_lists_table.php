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
        Schema::create('lead_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('original_filename');
            $table->integer('total_leads')->default(0);
            $table->integer('valid_leads')->default(0);
            $table->integer('invalid_leads')->default(0);
            $table->json('mapping_config'); // Configuração do mapeamento de colunas
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_lists');
    }
};
