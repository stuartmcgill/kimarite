<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\RikishiServiceFacade;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use StuartMcGill\SumoApiPhp\Service\RikishiService;
use Symfony\Component\HttpFoundation\JsonResponse;

class Head2HeadController extends Controller
{
    private readonly RikishiServiceFacade $rikishiService;

    public function __construct()
    {
        $this->rikishiService = new RikishiServiceFacade(RikishiService::factory());
    }

    public function view(): InertiaResponse
    {
        return Inertia::render('Head2Head', [
            'wrestlers' => $this->rikishiService->getMakuuchiWrestlers(),
        ]);
    }

    public function head2headsForWrestler(int $id): JsonResponse
    {
        return new JsonResponse(
            json_encode($this->rikishiService->getHead2headsForWrestler($id))
        );
    }
}
