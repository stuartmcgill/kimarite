<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\KimariteCount;
use App\Models\KimariteType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class KimariteController extends Controller
{
    public function show(Request $request): InertiaResponse
    {
        $bashos = KimariteCount::select('basho_id')->distinct()->orderBy('basho_id', 'desc')->get();

        return Inertia::render(
            'Kimarite',
            [
                'types' => KimariteType::all()->pluck('name'),
                'availableBashos' => $bashos->map(fn ($basho) => $basho['basho_id']),
            ],
        );
    }

    public function getCounts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'types' => 'required|array',
            'types.*' => 'string',
            'divisions' => 'required|array',
            'divisions.*' => 'string',
            'from' => 'required|date_format:Y-m',
            'to' => 'nullable|date_format:Y-m|after_or_equal:from',
            //'annual' => 'required|boolean',
            //'withPercentages' => 'required|boolean',
        ]);
    
        // Check for validation failure
        if ($validator->fails()) {
            return new JsonResponse([
                'error' => 'Invalid input data',
                'messages' => $validator->errors()
            ], 422);
        }

        $types = $request->input('types');
        $divisions = $request->input('divisions');
        $from = Str::replace('-', '', $request->input('from'));
        $to = Str::replace('-', '', $request->input('to'));
        $annual = (bool)$request->input('annual', false);
        $withPercentages = (bool)$request->input('withPercentages', true);

        // Consolidate across the divisions (i.e. group by type and basho ID)
        $cols = [
            'kc.type',
            'kc.basho_id',
            DB::raw('SUM(kc.count) AS total'),
        ];

        if ($withPercentages) {
            // Use MAX because we're aggregating, but there should only be one row per joined item
            $cols[] = DB::raw('SUM(kc.count) / bt.basho_total * 100 AS percentage');
        }
        
        $subquery = DB::table('basho_totals')
            ->select('basho_id', DB::raw('SUM(total) as basho_total'))
            ->whereIn('division', $divisions)
            ->groupBy('basho_id');

        $query = KimariteCount::from('kimarite_counts AS kc')
            ->select($cols)
            ->leftJoinSub($subquery, 'bt', function($join) {
                $join->on('kc.basho_id', '=', 'bt.basho_id');
            })
            ->whereIn('kc.type', $types)
            ->whereIn('kc.division', $divisions)
            ->where('kc.basho_id', '>=', $from);

        if (!empty($to)) {
            $query->where('kc.basho_id', '<=', $to);
        }

        $flatTotals = $query
            ->groupBy('type', 'basho_id')
            ->orderBy('basho_id')
            ->get();

        $allBashoIds = $flatTotals->pluck('basho_id')->unique()->values();

        // Structure the data by Kimarite type - send back an array with one entry per
        // type, containing all the counts/percentages for that kimarite
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
