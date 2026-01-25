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
        Schema::create('showdown_wrestlers', function (Blueprint $table) {
            $table->id();
            $table->integer('nsk_id');
            $table->integer('sumodb_id');
            $table->integer('sumoapi_id');
            $table->string('shikona_en');
            $table->string('shikona_jp');
            $table->string('shusshin');
            $table->string('stable');
            $table->string('rank');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showdown_wrestlers');
    }
};
