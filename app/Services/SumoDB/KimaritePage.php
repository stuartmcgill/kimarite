<?php

declare(strict_types=1);

namespace App\Services\SumoDB;

use App\Models\ShowdownWrestler;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use RuntimeException;

readonly class KimaritePage
{
    private string $baseUrl;

    private DOMXPath $xPath;

    public function __construct(private ShowdownWrestler $wrestler)
    {
        $this->baseUrl = config('custom.sumodb_base_url');
    }

    public function read(): self
    {
        $kimaritePage = "$this->baseUrl/Rikishi_kim.aspx?r=$this->wrestler->sumodb_id";
        $kimHtml = Http::get($kimaritePage)->body();
        $kimDoc = new DOMDocument();
        @$kimDoc->loadHTML($kimHtml);
        $this->xPath = new DOMXPath($kimDoc);

        return $this;
    }

    public function kimariteIndex(): float
    {
        $kv50Node = $this->xPath->query("//td[contains(@class, 'layoutleft')]//font[contains(., 'KV50')]")->item(0);
        if (!$kv50Node) {
            throw new RuntimeException("KV50 node not found for wrestler ID: {$wrestler->sumodb_id}");
        }

        preg_match('/KV50:\s*([\d.]+)/', $kv50Node->textContent, $matches);
        $kimariteIndex = (float) ($matches[1] ?? null);
        if (!$kimariteIndex) {
            throw new RuntimeException("Could not extract KV50 from regex for wrestler ID: {$wrestler->sumodb_id}");
        }

        return $kimariteIndex;
    }
}
