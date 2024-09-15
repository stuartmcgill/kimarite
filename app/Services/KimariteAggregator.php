<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\KimariteCount;
use Illuminate\Support\Collection;
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
}
