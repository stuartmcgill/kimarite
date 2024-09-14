<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\KimariteCount;
use App\Models\KimariteType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use StuartMcGill\SumoApiPhp\Service\KimariteService;

class RefreshController extends Controller
{
    public function __construct(private KimariteService $api) {}

    public function refresh(Request $request): InertiaResponse
    {
        return Inertia::render('Refresh');
    }

    public function rebuild(Request $request): JsonResponse
    {
        /** @var Collection<Collection<int>> */
        $counts = collect();

        $types = KimariteType::all();
        foreach ($types as $type) {
            $skip = 0;

            while (true) {
                $matches = collect($this->api->fetchByType(type: $type->name, limit: 1000, skip: $skip));
                if ($matches->count() === 0) {
                    break;
                }

                foreach ($matches as $match) {
                    $bashoId = $match->bashoId;
                    if (! $bashoId) {
                        continue;
                    }

                    // Set up the basho counts for this kimarite if they haven't already been done
                    $kimariteCounts = $counts->get($type, collect());
                    $kimariteBashoCount = $kimariteCounts->get($bashoId, 0);
                    $kimariteBashoCount++;

                    $kimariteCounts->put($bashoId, $kimariteBashoCount);
                    $counts->put($type, $kimariteCounts);
                }

                $skip += 1000;
                usleep(1 * 1000 * 1000);
            }
        }

        foreach ($counts as $type => $bashoCounts) {
            $bashoCounts->each(function ($count, $bashoId) use ($type) {
                KimariteCount::updateOrCreate([
                    'type' => $type,
                    'bashoId' => $bashoId,
                    'count' => $count,
                ]);
            });
        }

        return new JsonResponse([
            'message' => 'Success',
        ]);
    }
}
