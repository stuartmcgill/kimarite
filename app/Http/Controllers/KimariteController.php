<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\KimariteCount;
use App\Models\KimariteType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function getCounts(Request $request): JsonResponse
    {
        $types = $request->input('types');
        $divisions = $request->input('divisions');
        $from = Str::replace('-', '', $request->input('from'));
        $to = Str::replace('-', '', $request->input('to'));

        // Consolidate across the divisions (i.e. group by type and basho ID)
        $query = KimariteCount::select(
                'type',
                'basho_id',
                DB::raw('SUM(count) AS total'),
        );

        if (empty($to)) {
            $query->where('basho_id', '>=', $from);
        } else {
            $query->whereBetween('basho_id', [$from, $to]);
        }

        $flatTotals = $query->whereIn('type', $types)
        ->whereIn('division', $divisions)
        ->groupBy('type', 'basho_id')
        ->orderBy('basho_id')
        ->get();

        $allBashoIds = $flatTotals->pluck('basho_id')->unique()->values();

        // Structure the data by Kimarite type - send back an array with one entry per
        // type, containing all the counts for that kimarite
        $counts = collect($types)->map(fn (string $type) => [
            'type' => $type,
            'groupedCounts' => [],
        ]);

        foreach ($flatTotals as $flatTotal) {
            $key = $counts->search(fn (array $count) => Str::lower($count['type']) === $flatTotal->type);
            
            $counts->transform(function (array $count, int $index) use ($key, $flatTotal) {
                if ($index === $key) {
                    $count['groupedCounts'][] = $flatTotal;
                }

                return $count;
            });
        }

        return new JsonResponse([
            'counts' => $counts,
            'bashoIds' => $allBashoIds,
        ]);
    }
}
