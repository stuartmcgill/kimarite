<?php

declare(strict_types=1);

namespace App\Services\SumoDB;

use App\Models\ShowdownWrestler;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use RuntimeException;

readonly class RikishiPage
{
    private string $baseUrl;

    private DOMXPath $xPath;

    public function __construct(private ShowdownWrestler $wrestler)
    {
        $this->baseUrl = config('custom.sumodb_base_url');
    }

    public function read(): self
    {
        $rikishiPage = "$this->baseUrl/Rikishi.aspx?r=$this->wrestler->sumodb_id";
        $mainHtml = Http::get($rikishiPage)->body();
        $mainDoc = new DOMDocument();
        @$mainDoc->loadHTML($mainHtml);

        $this->xPath = new DOMXPath($mainDoc);

        return $this;
    }

    public function weight(): int
    {
        $weightNode = $this->xPath->query("//td[contains(text(), 'Weight')]/following-sibling::td")->item(0);
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

        return (int) $currentWeight;
    }

    /**
     * @return array{yusho:int, prizes:int}
     */
    public function awards(): array
    {
        // Makuuchi Yusho and special prizes
        $inMakuuchiNode = $this->xPath->query("//td[contains(text(), 'In Makuuchi')]/following-sibling::td")->item(0);
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

        return [
            'yusho' => $makuuchiYusho,
            'prizes' => $ginoSho + $shukunSho + $kantoSho,
        ];
    }

    /**
     * @return array{bouts:int, kyujo:float}
     */
    public function careerRecord(): array
    {
        // Career Record - extract total bouts and kyujo percentage
        $careerNode = $this->xPath->query("//td[contains(text(), 'Career Record')]/following-sibling::td")->item(0);
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

        return [
            'bouts' => $numBouts,
            'kyujo' => $kyujoPercentage,
        ];
    }
}
