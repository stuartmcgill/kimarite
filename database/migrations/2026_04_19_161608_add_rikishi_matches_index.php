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
        Schema::table('rikishi_matches', function (Blueprint $table) {
            $table->index(['basho_id', 'kimarite', 'division', 'day'], 'rikishi_matches_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rikishi_matches', function (Blueprint $table) {
            $table->dropIndex('rikishi_matches_lookup');
        });
    }
};
