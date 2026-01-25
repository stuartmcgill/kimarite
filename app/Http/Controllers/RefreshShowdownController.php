<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ShowdownWrestler;
use App\Models\ShowdownWrestlerCategory;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;
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
        $baseUrl = config('custom.sumodb_base_url');

        $rikishiPage = "$baseUrl/Rikishi.aspx?r=$wrestler->sumodb_id";
        $mainHtml = Http::get($rikishiPage)->body();
        $mainDoc = new DOMDocument;
        @$mainDoc->loadHTML($mainHtml);
        $mainXpath = new DOMXPath($mainDoc);

        $weightNode = $mainXpath->query("//td[contains(text(), 'Weight')]/following-sibling::td")->item(0);
        if (!$weightNode) {
            throw new RuntimeException("Weight node not found for wrestler ID: {$wrestler->sumodb_id}");
        }

        $weightText = $weightNode->textContent;

        // Look for e.g. 100 kg, if that fails look for e.g. 100.5 kg
        $result = preg_match('/cm (\d+) kg/', $weightText, $matches);
        if (!$result) {
            preg_match('/cm (\d+)\./', $weightText, $matches);
        }

        $currentWeight = $matches[1] ?? null;
        if (is_null($currentWeight)) {
            throw new RuntimeException("Could not extract weight from regex for wrestler ID: {$wrestler->sumodb_id}");
        }

        $wrestler->categories->push(new ShowdownWrestlerCategory([
            'code' => 'weight',
            'value' => $currentWeight,
        ]));

        // Makuuchi Yusho and special prizes
        $inMakuuchiNode = $mainXpath->query("//td[contains(text(), 'In Makuuchi')]/following-sibling::td")->item(0);
        if (!$inMakuuchiNode) {
            throw new RuntimeException("Makuuchi node not found for wrestler ID: {$wrestler->sumodb_id}");
        }

        $makuuchiNodeText = trim($inMakuuchiNode->textContent);

        preg_match('/(\d+)\s+Yusho,/', $makuuchiNodeText, $yushoMatch);
        preg_match('/(\d+)\s+Gino-Sho/', $makuuchiNodeText, $ginoMatch);
        preg_match('/(\d+)\s+Shukun-Sho/', $makuuchiNodeText, $shukunMatch);
        preg_match('/(\d+)\s+Kanto-Sho/', $makuuchiNodeText, $kantoMatch);

        $makuuchiYusho = (int) ($yushoMatch[1] ?? 0);
        $ginoSho = (int) ($ginoMatch[1] ?? 0);
        $shukunSho = (int) ($shukunMatch[1] ?? 0);
        $kantoSho = (int) ($kantoMatch[1] ?? 0);

        $totalSpecialPrizes = $ginoSho + $shukunSho + $kantoSho;

        $wrestler->categories->push(new ShowdownWrestlerCategory([
            'code' => 'yusho',
            'value' => $makuuchiYusho,
        ]));

        $wrestler->categories->push(new ShowdownWrestlerCategory([
            'code' => 'prizes',
            'value' => $totalSpecialPrizes,
        ]));

        // Career Record - extract total bouts and kyujo percentage
        $careerNode = $mainXpath->query("//td[contains(text(), 'Career Record')]/following-sibling::td")->item(0);
        if (!$careerNode) {
            throw new RuntimeException("Career node not found for wrestler ID: {$wrestler->sumodb_id}");
        }

        $careerText = trim($careerNode->textContent);

        // Pattern: wins-losses(-absences)/total
        if (!preg_match('/\d+-\d+(?:-(\d+))?\/(\d+)/', $careerText, $matches)) {
            throw new RuntimeException("Could not extract bouts/kyujo from regex for wrestler ID: {$wrestler->sumodb_id}");
        }

        $numBouts = (int) $matches[2];
        $absences = (int) ($matches[1] ?? 0);
        $kyujoPercentage = $numBouts > 0 ? round(($absences / $numBouts) * 100, 2) : 0;

        $wrestler->categories->push(new ShowdownWrestlerCategory([
            'code' => 'bouts',
            'value' => $numBouts,
        ]));

        $wrestler->categories->push(new ShowdownWrestlerCategory([
            'code' => 'kyujo',
            'value' => $kyujoPercentage,
        ]));

        // Kimarite index (KV50)
        // Fetch kimarite page
        $kimaritePage = "$baseUrl/Rikishi_kim.aspx?r=$wrestler->sumodb_id";
        $kimHtml = Http::get($kimaritePage)->body();
        $kimDoc = new DOMDocument;
        @$kimDoc->loadHTML($kimHtml);
        $kimXpath = new DOMXPath($kimDoc);

        $kv50Node = $kimXpath->query("//td[contains(@class, 'layoutleft')]//font[contains(., 'KV50')]")->item(0);
        if (!$kv50Node) {
            throw new RuntimeException("KV50 node not found for wrestler ID: {$wrestler->sumodb_id}");
        }

        preg_match('/KV50:\s*([\d.]+)/', $kv50Node->textContent, $matches);
        $kimariteIndex = (float) ($matches[1] ?? null);

        if (!$kimariteIndex) {
            throw new RuntimeException("Could not extract KV50 from regex for wrestler ID: {$wrestler->sumodb_id}");
        }

        $wrestler->categories->push(new ShowdownWrestlerCategory([
            'code' => 'kimarite',
            'value' => $kimariteIndex,
        ]));
    }
}
