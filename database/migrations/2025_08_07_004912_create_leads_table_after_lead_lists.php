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
        Schema::dropIfExists('leads');
        
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_list_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('phone_number');
            $table->string('product')->nullable();
            $table->json('extra_data')->nullable(); // Para campos adicionais do CSV
            $table->timestamps();
            
            $table->index(['lead_list_id', 'phone_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
