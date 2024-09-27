<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\KimariteCount;
use App\Models\KimariteType;
use App\Services\KimariteAggregator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        /** @var Collection<RikishiMatch> */
        $allMatches = collect();

        $types = KimariteType::all();
        foreach ($types as $type) {
            $skip = 0;

            while (true) {
                $name = $type->name;
                $kimariteMatches = collect($this->api->fetchByType(type: $name, limit: 1000, skip: $skip));
                if ($kimariteMatches->count() === 0) {
                    break;
                }

                foreach ($kimariteMatches as $match) {
                    $bashoId = $match->bashoId;
                    if (! $bashoId) {
                        continue;
                    }

                    if (! preg_match('/^\d{6}$/', $bashoId)) {
                        continue;
                    }

                    $allMatches->push($match);
                    //                    // Set up the basho counts for this kimarite if they haven't already been done
                    //                    $kimariteCounts = $counts->get($name, collect());
                    //                    $kimariteBashoCount = $kimariteCounts->get($bashoId, 0);
                    //                    $kimariteBashoCount++;
                    //
                    //                    $kimariteCounts->put($bashoId, $kimariteBashoCount);
                    //                    $counts->put($name, $kimariteCounts);
                }

                $skip += 1000;
                usleep(1 * 1000 * 1000);
            }
        }

        $this->aggregator->aggregateAndStoreCounts($allMatches);

        //        foreach ($counts as $type => $bashoCounts) {
        //            $bashoCounts->each(function ($count, $bashoId) use ($type) {
        //                KimariteCount::updateOrCreate([
        //                    'type' => $type,
        //                    'bashoId' => $bashoId,
        //                    'count' => $count,
        //                ]);
        //            });
        //        }

        return new JsonResponse([
            'message' => 'Success',
        ]);
    }
}
