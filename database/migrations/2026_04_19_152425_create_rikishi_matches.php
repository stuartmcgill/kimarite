<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rikishi_matches', function (Blueprint $table) {
            $table->id();
            $table->string('basho_id');
            $table->string('division');
            $table->unsignedTinyInteger('day');
            $table->unsignedBigInteger('east_id');
            $table->string('east_shikona');
            $table->string('east_rank');
            $table->unsignedBigInteger('west_id');
            $table->string('west_shikona');
            $table->string('west_rank');

            $table->string('kimarite');
            $table->foreign('kimarite')->references('name')->on('kimarite_types');

            $table->unsignedBigInteger('winner_id');
            $table->string('winner_en');
            $table->string('winner_jp');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rikishi_matches');
    }
};
