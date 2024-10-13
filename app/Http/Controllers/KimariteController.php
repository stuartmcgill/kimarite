<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\KimariteCount;
use App\Models\KimariteType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        $rawCounts = KimariteCount::whereBetween('basho_id', [$from, $to])
            ->whereIn('type', $types)
            ->whereIn('division', $divisions)
            ->orderBy('basho_id')
            ->get();

        // Note there could be gaps for rarer kimarite
        $bashoIds = $rawCounts->pluck('basho_id')->unique()->values();

        // Group by Kimarite type
        $groupedCounts = $rawCounts->reduce(function (Collection $groupedCounts, KimariteCount $count) {
            $runningCountsForType = $groupedCounts->get($count->type, collect());
            $runningCountsForType->add($count);
            
            $groupedCounts->put($count->type, $runningCountsForType);

            return $groupedCounts;
        }, collect());

        return new JsonResponse([
            'counts' => $groupedCounts,
            'bashoIds' => $bashoIds,
        ]);
    }
}
