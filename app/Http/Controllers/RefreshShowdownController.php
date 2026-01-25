<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ShowdownWrestler;
use App\Models\ShowdownWrestlerCategory;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use StuartMcGill\SumoApiPhp\Service\RikishiService;

ini_set('memory_limit', '512M');

class RefreshShowdownController extends Controller
{
    public function refresh(Request $request): JsonResponse
    {
        // Allow 45 mins
        set_time_limit(60 * 45);
        logger()->info('Rebuilding Sumo Showdown data');

        //        ShowdownWrestlerCategory::truncate();
        //        ShowdownWrestler::truncate();

        $this->fetchData();

        return new JsonResponse([
            'message' => 'Success',
        ]);
    }

    private function fetchData(): void
    {
        $rikishiService = RikishiService::factory();

        $wrestlers = $rikishiService->fetchDivision('Makuuchi');
        foreach ($wrestlers as $wrestler) {
            $wrestler = ShowdownWrestler::create([
                'nsk_id' => $wrestler->nskId,
                'sumodb_id' => $wrestler->sumoDbId,
                'sumoapi_id' => $wrestler->id,
                'shikona_en' => $wrestler->shikonaEn,
                'shikona_jp' => $wrestler->shikonaJp,
                'shusshin' => $wrestler->shusshin,
                'stable' => $wrestler->heya,
                'rank' => $wrestler->currentRank,
            ]);

            $this->fetchCategories($wrestler);
        }
    }

    private function fetchCategories(ShowdownWrestler $wrestler): void
    {
        $categories = [];

        $baseUrl = config('custom.sumodb_base_url');
        $rikishiPage = "$baseUrl/Rikishi.aspx?r=$wrestler->sumodb_id";
        $kimaritePage = "$baseUrl/Rikishi_kim.aspx?r=$wrestler->sumodb_id";

        $mainHtml = Http::get($rikishiPage)->body();
        $mainDoc = new DOMDocument;
        @$mainDoc->loadHTML($mainHtml);
        $mainXpath = new DOMXPath($mainDoc);

        // Fetch kimarite page
        $kimHtml = Http::get($kimaritePage)->body();
        $kimDoc = new DOMDocument;
        @$kimDoc->loadHTML($kimHtml);
        $kimXpath = new DOMXPath($kimDoc);

        // Current weight
        $currentWeight = null;
        $weightNode = $mainXpath->query("//td[contains(text(), 'Weight')]/following-sibling::td")->item(0);
        if ($weightNode) {
            preg_match('/cm (\d+) kg/', $weightNode->textContent, $matches);
            $currentWeight = $matches[1] ?? null;

            $categories[] = ShowdownWrestlerCategory::make([
                'showdown_wrestler_id' => $wrestler->id,
                'code' => 'weight',
                'value' => $currentWeight,
            ]);
        }

        // No. Makuuchi Yusho
        $makuuchiYusho = 0;
        $inMakuuchiNode = $mainXpath->query("//td[contains(text(), 'In Makuuchi')]/following-sibling::td")->item(0);
        if ($inMakuuchiNode) {
            $makuuchiNodeText = trim($inMakuuchiNode->textContent);

            // @TODO SJM carry on from here
            preg_match('/(\d+)-(\d+)(-\d+)?\/(\d+)/', $makuuchiNodeText, $matches);

            $categories[] = ShowdownWrestlerCategory::make([
                'showdown_wrestler_id' => $wrestler->id,
                'code' => 'yusho',
                'value' => $makuuchiYusho,
            ]);

            // Total special prizes
            $totalSpecialPrizes = 0;
            $specialPrizes = ['Gino-Sho', 'Shukun-Sho', 'Kanto-Sho'];
            foreach ($specialPrizes as $prize) {
                $prizeNode = $mainXpath->query("//td[contains(text(), '$prize')]/following-sibling::td")->item(0);
                if ($prizeNode) {
                    $totalSpecialPrizes += (int) trim($prizeNode->textContent);
                }
            }

            $categories[] = ShowdownWrestlerCategory::make([
                'showdown_wrestler_id' => $wrestler->id,
                'code' => 'prizes',
                'value' => $totalSpecialPrizes,
            ]);
        }

        // Career Record - extract total bouts and kyuji percentage
        $noBouts = null;
        $kyujoPercentage = null;
        $careerNode = $mainXpath->query("//td[contains(text(), 'Career Record')]/following-sibling::td")->item(0);
        if ($careerNode) {
            $careerText = trim($careerNode->textContent);
            // Pattern: wins-losses-absences/total
            if (preg_match('/\d+-\d+(?:-(\d+))?\/(\d+)/', $careerText, $matches)) {
                $noBouts = (int) $matches[2];
                $absences = (int) ($matches[1] ?? 0);
                $kyujoPercentage = $noBouts > 0 ? round(($absences / $noBouts) * 100, 2) : 0;

                $categories[] = ShowdownWrestlerCategory::make([
                    'showdown_wrestler_id' => $wrestler->id,
                    'code' => 'bouts',
                    'value' => $noBouts,
                ]);

                $categories[] = ShowdownWrestlerCategory::make([
                    'showdown_wrestler_id' => $wrestler->id,
                    'code' => 'kyujo',
                    'value' => $kyujoPercentage,
                ]);
            }
        }

        // Kimarite index (KV50)
        $kimariteIndex = null;
        $kv50Node = $kimXpath->query("//td[contains(text(), 'KV50')]/following-sibling::td")->item(0);
        if ($kv50Node) {
            $kimariteIndex = (float) trim($kv50Node->textContent);

            $categories[] = ShowdownWrestlerCategory::make([
                'showdown_wrestler_id' => $wrestler->id,
                'code' => 'kimarite',
                'value' => $kimariteIndex,
            ]);
        }

        foreach ($categories as $category) {
            $category->save();
        }
    }
}
