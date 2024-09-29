<?php

declare(strict_types=1);

namespace App\Services;

use StuartMcGill\SumoApiPhp\Model\MatchupSummary;
use StuartMcGill\SumoApiPhp\Model\Rikishi;
use StuartMcGill\SumoApiPhp\Service\RikishiService;

class RikishiServiceFacade
{
    public function __construct(private readonly RikishiService $service) {}

    /** @return list<Rikishi> */
    public function getMakuuchiWrestlers(): array
    {
        $wrestlers = $this->service->fetchDivision('Makuuchi');

        usort(
            array: $wrestlers,
            callback: static function (Rikishi $a, Rikishi $b) {
                return $a->shikonaEn <=> $b->shikonaEn;
            },
        );

        return $wrestlers;
    }

    public function getHead2headsForWrestler(int $id): MatchupSummary
    {
        $wrestlers = $this->getMakuuchiWrestlers();
        $otherIds = array_map(static fn ($wrestler) => $wrestler->id, $wrestlers);

        return $this->service->fetchMatchups(rikishiId: $id, opponentIds: $otherIds);
    }
}
