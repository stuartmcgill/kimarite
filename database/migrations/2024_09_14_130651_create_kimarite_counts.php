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
        Schema::create('kimarite_counts', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('basho_id');
            $table->string('division');
            $table->unsignedInteger('count');
            $table->timestamps();

            $table->foreign('type')
                ->references('name')
                ->on('kimarite_types')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kimarite_counts');
    }
};
