<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ShowdownWrestler;
use App\Models\ShowdownWrestlerCategory;
use App\Services\SumoDB\KimaritePage;
use App\Services\SumoDB\RikishiPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use StuartMcGill\SumoApiPhp\Service\RikishiService;
use Throwable;

ini_set('memory_limit', '512M');

class RefreshShowdownController extends Controller
{
    public function refresh(Request $request): JsonResponse
    {
        try {
            // Allow 5 mins
            set_time_limit(60 * 5);
            logger()->info('Rebuilding Sumo Showdown data');

            $this->fetchData();

            return response()->json(['message' => 'Successfully refreshed Sumo Showdown data']);
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => $e->getMessage()]);
        }
    }

    private function fetchData(): void
    {
        $rikishiService = RikishiService::factory();
        $wrestlers = collect();

        $apiWrestlers = $rikishiService->fetchDivision('Makuuchi');
        foreach ($apiWrestlers as $apiWrestler) {
            $wrestler = ShowdownWrestler::make([
                'nsk_id' => $apiWrestler->nskId,
                'sumodb_id' => $apiWrestler->sumoDbId,
                'sumoapi_id' => $apiWrestler->id,
                'shikona_en' => $apiWrestler->shikonaEn,
                'shikona_jp' => $apiWrestler->shikonaJp,
                'shusshin' => $apiWrestler->shusshin,
                'stable' => $apiWrestler->heya,
                'rank' => $apiWrestler->currentRank,
            ]);

            $this->buildCategories($wrestler);
            $wrestlers->push($wrestler);

            usleep(1000 * 500); // 0.5s rate limit
        }

        DB::transaction(function () use ($wrestlers) {
            ShowdownWrestlerCategory::query()->delete();
            ShowdownWrestler::query()->delete();

            foreach ($wrestlers as $wrestler) {
                $wrestler->save();

                foreach ($wrestler->categories as $category) {
                    $category->showdown_wrestler_id = $wrestler->id;
                    $category->save();
                }
            }
        });
    }

    private function buildCategories(ShowdownWrestler $wrestler): void
    {
        $rikishiPage = new RikishiPage($wrestler);
        $rikishiPage->read();

        $awards = $rikishiPage->awards();
        $careerRecord = $rikishiPage->careerRecord();

        $categories = [
            [
                'code' => 'weight',
                'value' => $rikishiPage->weight(),
            ],
            [
                'code' => 'yusho',
                'value' => $awards['yusho'],
            ],
            [
                'code' => 'prizes',
                'value' => $awards['prizes'],
            ],
            [
                'code' => 'bouts',
                'value' => $careerRecord['bouts'],
            ],
            [
                'code' => 'kyujo',
                'value' => $careerRecord['kyujo'],
            ],
            [
                'code' => 'kimarite',
                'value' => $this->getKimariteIndex($wrestler),
            ],
        ];

        foreach ($categories as $category) {
            $wrestler->categories->push(new ShowdownWrestlerCategory($category));
        }
    }

    private function getKimariteIndex(ShowdownWrestler $wrestler): float
    {
        $kimaritePage = new KimaritePage($wrestler);
        $kimaritePage->read();

        return $kimaritePage->kimariteIndex();
    }
}
