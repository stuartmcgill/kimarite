<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\KimariteCount;
use App\Models\KimariteType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class KimariteController extends Controller
{
    public function show(Request $request): InertiaResponse
    {
        $bashos = KimariteCount::select('basho_id')->distinct()->orderBy('basho_id')->get();

        //$counts = KimariteCount::all();

        return Inertia::render(
            'Kimarite',
            [
                'types' => KimariteType::all()->pluck('name'),
                'availableBashos' => $bashos->map(fn ($basho) => $basho['basho_id']),
                //'counts' => $counts,
            ],
        );
    }

    public function getStats(Request $request): JsonResponse
    {
        $types = $request->input('types');
        $divisions = $request->input('divisions');
        $from = Str::replace('-', '', $request->input('from'));
        $to = Str::replace('-', '', $request->input('to'));

        // Consolidate across the divisions (i.e. group by type and basho ID)
        $rawCounts = KimariteCount::select(
                'type',
                'basho_id',
                DB::raw('SUM(count) AS total'),
            )
            ->whereBetween('basho_id', [$from, $to])
            ->whereIn('type', $types)
            ->whereIn('division', $divisions)
            ->groupBy('type', 'basho_id')
            ->orderBy('basho_id')
            ->get();

        // Note there could be gaps for rarer kimarite
        $allBashoIds = $rawCounts->pluck('basho_id')->unique()->values();

        // Group by Kimarite type
        $groupedCounts = $rawCounts->reduce(function (Collection $groupedCounts, $row) {
            $runningCountsForType = $groupedCounts->get($row->type, collect());
            $runningCountsForType->add($row);
            
            $groupedCounts->put($row->type, $runningCountsForType);

            return $groupedCounts;
        }, collect());

        return new JsonResponse([
            'counts' => $groupedCounts,
            'bashoIds' => $allBashoIds,
        ]);
    }
}
