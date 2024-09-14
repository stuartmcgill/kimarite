<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use StuartMcGill\SumoApiPhp\Model\RikishiMatch;
use StuartMcGill\SumoApiPhp\Service\KimariteService;

class KimariteApiService
{
    /**
     * @return Collection<RikishiMatch>
     */
    public function fetchByType(): Collection
    {
        $api = KimariteService::factory();
        $matches = $api->fetchByType('yorikiri');

        return collect($matches);
    }
}
