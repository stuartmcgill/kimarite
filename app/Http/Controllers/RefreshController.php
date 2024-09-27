<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\KimariteType;
use App\Models\Run;
use App\Services\KimariteAggregator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use StuartMcGill\SumoApiPhp\Model\RikishiMatch;
use StuartMcGill\SumoApiPhp\Service\KimariteService;

ini_set('memory_limit', '512M');

class RefreshController extends Controller
{
    public function __construct(
        private KimariteService $api,
        private KimariteAggregator $aggregator,
    ) {}

    public function refresh(Request $request): InertiaResponse
    {
        return Inertia::render('Refresh');
    }

    public function rebuild(Request $request): JsonResponse
    {
        // Allow 45 mins
        set_time_limit(60 * 45);

        logger()->info('Rebuilding Kimarite data');
        $run = Run::create();

        $types = KimariteType::all();
        foreach ($types as $type) {
            /** @var Collection<RikishiMatch> */
            $typeMatches = collect();

            logger()->info('Processing data for '.$type->name);
            $skip = 0;

            while (true) {
                $name = $type->name;
                $matches = collect($this->api->fetchByType(type: $name, limit: 1000, skip: $skip));
                if ($matches->count() === 0) {
                    break;
                }

                foreach ($matches as $match) {
                    $bashoId = $match->bashoId;
                    if (! $bashoId) {
                        continue;
                    }

                    if (! preg_match('/^\d{6}$/', $bashoId)) {
                        continue;
                    }

                    $typeMatches->push($match);
                }

                // Move on to the next batch
                $skip += 1000;

                // Wait 0.5 second before calling API again
                usleep(1 * 1000 * 500);
            }

            $this->aggregator->aggregateAndStoreCounts($typeMatches);
        }

        $run->completed_at = Carbon::now();
        $run->save();

        return new JsonResponse([
            'message' => 'Success',
        ]);
    }
}
