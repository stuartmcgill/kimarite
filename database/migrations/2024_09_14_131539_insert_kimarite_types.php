<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('kimarite_types')->insert(['name' => 'abisetaoshi']);
        DB::table('kimarite_types')->insert(['name' => 'amiuchi']);
        DB::table('kimarite_types')->insert(['name' => 'ashitori']);
        DB::table('kimarite_types')->insert(['name' => 'chongake']);
        DB::table('kimarite_types')->insert(['name' => 'dashinage']);
        DB::table('kimarite_types')->insert(['name' => 'fumidashi']);
        DB::table('kimarite_types')->insert(['name' => 'fusen']);
        DB::table('kimarite_types')->insert(['name' => 'gasshohineri']);
        DB::table('kimarite_types')->insert(['name' => 'hansoku']);
        DB::table('kimarite_types')->insert(['name' => 'harimanage']);
        DB::table('kimarite_types')->insert(['name' => 'hatakikomi']);
        DB::table('kimarite_types')->insert(['name' => 'hikiotoshi']);
        DB::table('kimarite_types')->insert(['name' => 'hikiwake']);
        DB::table('kimarite_types')->insert(['name' => 'hikkake']);
        DB::table('kimarite_types')->insert(['name' => 'ipponzeoi']);
        DB::table('kimarite_types')->insert(['name' => 'isamiashi']);
        DB::table('kimarite_types')->insert(['name' => 'itamiwake']);
        DB::table('kimarite_types')->insert(['name' => 'izori']);
        DB::table('kimarite_types')->insert(['name' => 'kainahineri']);
        DB::table('kimarite_types')->insert(['name' => 'kakenage']);
        DB::table('kimarite_types')->insert(['name' => 'katasukashi']);
        DB::table('kimarite_types')->insert(['name' => 'kawazugake']);
        DB::table('kimarite_types')->insert(['name' => 'kekaeshi']);
        DB::table('kimarite_types')->insert(['name' => 'ketaguri']);
        DB::table('kimarite_types')->insert(['name' => 'kimedashi']);
        DB::table('kimarite_types')->insert(['name' => 'kimetaoshi']);
        DB::table('kimarite_types')->insert(['name' => 'kirikaeshi']);
        DB::table('kimarite_types')->insert(['name' => 'komatasukui']);
        DB::table('kimarite_types')->insert(['name' => 'koshikudake']);
        DB::table('kimarite_types')->insert(['name' => 'koshinage']);
        DB::table('kimarite_types')->insert(['name' => 'kotehineri']);
        DB::table('kimarite_types')->insert(['name' => 'kotenage']);
        DB::table('kimarite_types')->insert(['name' => 'kozumatori']);
        DB::table('kimarite_types')->insert(['name' => 'kubihineri']);
        DB::table('kimarite_types')->insert(['name' => 'kubinage']);
        DB::table('kimarite_types')->insert(['name' => 'makiotoshi']);
        DB::table('kimarite_types')->insert(['name' => 'mitokorozeme']);
        DB::table('kimarite_types')->insert(['name' => 'nichonage']);
        DB::table('kimarite_types')->insert(['name' => 'nimaigeri']);
        DB::table('kimarite_types')->insert(['name' => 'okuridashi']);
        DB::table('kimarite_types')->insert(['name' => 'okurigake']);
        DB::table('kimarite_types')->insert(['name' => 'okurihikiotoshi']);
        DB::table('kimarite_types')->insert(['name' => 'okurinage']);
        DB::table('kimarite_types')->insert(['name' => 'okuritaoshi']);
        DB::table('kimarite_types')->insert(['name' => 'okuritsuridashi']);
        DB::table('kimarite_types')->insert(['name' => 'okuritsuriotoshi']);
        DB::table('kimarite_types')->insert(['name' => 'omata']);
        DB::table('kimarite_types')->insert(['name' => 'osakate']);
        DB::table('kimarite_types')->insert(['name' => 'oshidashi']);
        DB::table('kimarite_types')->insert(['name' => 'oshitaoshi']);
        DB::table('kimarite_types')->insert(['name' => 'sabaori']);
        DB::table('kimarite_types')->insert(['name' => 'sakatottari']);
        DB::table('kimarite_types')->insert(['name' => 'shitatedashinage']);
        DB::table('kimarite_types')->insert(['name' => 'shitatehineri']);
        DB::table('kimarite_types')->insert(['name' => 'shitatenage']);
        DB::table('kimarite_types')->insert(['name' => 'sokubiotoshi']);
        DB::table('kimarite_types')->insert(['name' => 'sotogake']);
        DB::table('kimarite_types')->insert(['name' => 'sotokomata']);
        DB::table('kimarite_types')->insert(['name' => 'sotomuso']);
        DB::table('kimarite_types')->insert(['name' => 'sukuinage']);
        DB::table('kimarite_types')->insert(['name' => 'susoharai']);
        DB::table('kimarite_types')->insert(['name' => 'susotori']);
        DB::table('kimarite_types')->insert(['name' => 'tasukizori']);
        DB::table('kimarite_types')->insert(['name' => 'tokkurinage']);
        DB::table('kimarite_types')->insert(['name' => 'tottari']);
        DB::table('kimarite_types')->insert(['name' => 'tsukaminage']);
        DB::table('kimarite_types')->insert(['name' => 'tsukidashi']);
        DB::table('kimarite_types')->insert(['name' => 'tsukihiza']);
        DB::table('kimarite_types')->insert(['name' => 'tsukiotoshi']);
        DB::table('kimarite_types')->insert(['name' => 'tsukitaoshi']);
        DB::table('kimarite_types')->insert(['name' => 'tsukite']);
        DB::table('kimarite_types')->insert(['name' => 'tsumatori']);
        DB::table('kimarite_types')->insert(['name' => 'tsuridashi']);
        DB::table('kimarite_types')->insert(['name' => 'tsuriotoshi']);
        DB::table('kimarite_types')->insert(['name' => 'tsutaezori']);
        DB::table('kimarite_types')->insert(['name' => 'uchigake']);
        DB::table('kimarite_types')->insert(['name' => 'uchimuso']);
        DB::table('kimarite_types')->insert(['name' => 'ushiromotare']);
        DB::table('kimarite_types')->insert(['name' => 'utchari']);
        DB::table('kimarite_types')->insert(['name' => 'uwatedashinage']);
        DB::table('kimarite_types')->insert(['name' => 'uwatehineri']);
        DB::table('kimarite_types')->insert(['name' => 'uwatenage']);
        DB::table('kimarite_types')->insert(['name' => 'waridashi']);
        DB::table('kimarite_types')->insert(['name' => 'watashikomi']);
        DB::table('kimarite_types')->insert(['name' => 'yaguranage']);
        DB::table('kimarite_types')->insert(['name' => 'yobikaeshi']);
        DB::table('kimarite_types')->insert(['name' => 'yobimodoshi']);
        DB::table('kimarite_types')->insert(['name' => 'yorikiri']);
        DB::table('kimarite_types')->insert(['name' => 'yoritaoshi']);
        DB::table('kimarite_types')->insert(['name' => 'zubuneri']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('kimarite_types')->truncate();
    }
};
