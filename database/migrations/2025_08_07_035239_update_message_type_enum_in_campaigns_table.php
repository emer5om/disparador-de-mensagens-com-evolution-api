<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Para MySQL, precisamos alterar o enum usando SQL raw
        DB::statement("ALTER TABLE campaigns MODIFY COLUMN message_type ENUM('text', 'button', 'poll', 'list', 'url_button') DEFAULT 'text'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para o enum original
        DB::statement("ALTER TABLE campaigns MODIFY COLUMN message_type ENUM('text', 'button', 'poll', 'list') DEFAULT 'text'");
    }
};
