<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BashoTotal;
use App\Models\KimariteCount;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use StuartMcGill\SumoApiPhp\Model\RikishiMatch;

class KimariteAggregator
{
    /**
     * @param  Collection<RikishiMatch>  $matches
     */
    public function aggregateAndStoreCounts(Collection $matches): void
    {
        // Group matches by kimarite, bashoId, and division
        $grouped = $matches->groupBy(
            fn (RikishiMatch $match) => "{$match->kimarite}_{$match->bashoId}_{$match->division}"
        );

        $grouped->each(function ($groupedMatches, $key) {
            $count = $groupedMatches->count();

            [$kimarite, $bashoId, $division] = explode('_', $key);

            KimariteCount::create([
                'type' => $kimarite,
                'basho_id' => $bashoId,
                'division' => $division,
                'count' => $count,
            ]);
        });
    }

    public function refreshBashoPercentages(): void
    {
        $this->refreshBashoTotals();
    
        DB::table('kimarite_counts as kc')
            ->join('basho_totals as bt', fn ($join) =>
                $join->on('bt.basho_id', '=', 'kc.basho_id')
                    ->on('bt.division', '=', 'kc.division')
            )
            ->update([
                'kc.percentage' => DB::raw('kc.count / bt.total')
            ]);
    }

    public function refreshAnnualPercentages(): void
    {
        $this->refreshBashoTotals();

        $annualCounts = DB::table('kimarite_counts')
            ->select(DB::raw('SUBSTRING(basho_id, 1, 4) as year'), DB::raw('SUM(count) as annual_total'))
            ->groupBy(DB::raw('SUBSTRING(basho_id, 1, 4)'))
            ->get();
    }

    private function refreshBashoTotals(): void
    {
        BashoTotal::truncate();

        DB::table('basho_totals')->insertUsing(
            ['basho_id', 'division', 'total'],
            DB::table('kimarite_counts')
                ->select('basho_id', 'division', DB::raw('SUM(count) as total'))
                ->groupBy(['basho_id', 'division'])
        );
    }
}
