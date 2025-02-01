<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\RikishiServiceFacade;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use StuartMcGill\SumoApiPhp\Model\Matchup;
use StuartMcGill\SumoApiPhp\Model\MatchupSummary;
use StuartMcGill\SumoApiPhp\Model\Rikishi;
use StuartMcGill\SumoApiPhp\Service\RikishiService;

class RikishiServiceFacadeTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function getMakuuchiWrestlers(): void
    {
        $service = $this->mockServiceForGetMakuuchiWrestlers([
            ['id' => 1, 'shikonaEn' => 'Hakuho'],
            ['id' => 2, 'shikonaEn' => 'Kakuryu'],
        ]);
        $facade = new RikishiServiceFacade($service);
        $wrestlers = $facade->getMakuuchiWrestlers();

        $this->assertCount(2, $wrestlers);
    }

    /** @test */
    public function getHead2headsForWrestler(): void
    {
        $matchups = [
            new Matchup(rikishiId: 3, opponentId: 1, rikishiWins: 1, opponentWins: 1),
            new Matchup(rikishiId: 3, opponentId: 2, rikishiWins: 2, opponentWins: 2),
        ];

        $service = $this->mockServiceForGetMakuuchiWrestlers([
            ['id' => 1, 'shikonaEn' => 'Hakuho'],
            ['id' => 2, 'shikonaEn' => 'Kakuryu'],
        ]);
        $service
            ->expects('fetchMatchups')
            ->with(3, [1, 2])
            ->andReturn(
                new MatchupSummary(rikishiId: 3, matchups: $matchups)
            );

        $facade = new RikishiServiceFacade($service);
        $summary = $facade->getHead2headsForWrestler(id: 3);

        $this->assertSame(expected: 3, actual: $summary->rikishiId);
        $this->assertSame(expected: $matchups, actual: $summary->matchups);
    }

    /** @param list<array<string, mixed>> $wrestlers */
    private function mockServiceForGetMakuuchiWrestlers(
        array $wrestlers
    ): RikishiService|MockInterface {
        $service = Mockery::mock(RikishiService::class);
        $service
            ->expects('fetchDivision')
            ->with('Makuuchi')
            ->andReturn(array_values(array_map(
                callback: static fn (array $wrestler) => self::generateRikishi($wrestler['id'], $wrestler['shikonaEn']),
                array: $wrestlers,
            )));

        return $service;
    }

    private static function generateRikishi(int $id, string $shikonaEn): Rikishi
    {
        return new Rikishi(
            id: $id,
            sumoDbId: null,
            nskId: null,
            shikonaEn: $shikonaEn,
            shikonaJp: null,
            currentRank: null,
            heya: null,
            birthDate: null,
            shusshin: null,
            height: null,
            weight: null,
            debut: '2023-01',
        );
    }
}
