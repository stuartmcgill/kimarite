<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\KimariteCount;
use App\Models\KimariteType;
use App\Models\Run;
use App\Services\KimariteAggregator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use StuartMcGill\SumoApiPhp\Model\RikishiMatch;
use StuartMcGill\SumoApiPhp\Service\KimariteService;

class KimariteController extends Controller
{
    public function __construct(
        
    ) {}

    public function show(Request $request): InertiaResponse
    {
        $bashos = KimariteCount::select('basho_id')->distinct()->orderBy('basho_id')->get();

        return Inertia::render(
            'Kimarite',
            [
                'types' => KimariteType::all()->pluck('name'),
                'availableBashos' => $bashos->map(fn ($basho) => $basho['basho_id']),
            ],
        );
    }

    public function getStats(Request $request): JsonResponse
    {
        $types = $request->input('types');
        Log::info($types);
        $from = $request->input('from');
        $to = $request->input('to');

        $bashoKimariteCounts = KimariteCount::whereBetween('basho_id', [$from, $to])
            ->whereIn('type', $types)
            ->get();

        return new JsonResponse([
            'stats' => $bashoKimariteCounts,
        ]);
    }
}
