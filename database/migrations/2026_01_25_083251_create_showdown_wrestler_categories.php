<?php

declare(strict_types=1);

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
        Schema::create('showdown_wrestler_categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('showdown_wrestler_id')->constrained('showdown_wrestlers');
            $table->string('code');
            $table->decimal('value', 8, 1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showdown_wrestler_categories');
    }
};
