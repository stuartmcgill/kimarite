<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $matches = $this->api->fetchByType('yorikiri');

        return new JsonResponse([
            'message' => 'Success',
        ]);
    }
}
