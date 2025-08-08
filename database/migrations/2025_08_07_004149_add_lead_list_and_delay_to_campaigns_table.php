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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->foreignId('lead_list_id')->nullable()->after('instance_id')->constrained()->onDelete('set null');
            $table->integer('delay_seconds')->default(3)->after('buttons'); // Delay entre mensagens
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropForeign(['lead_list_id']);
            $table->dropColumn(['lead_list_id', 'delay_seconds']);
        });
    }
};
