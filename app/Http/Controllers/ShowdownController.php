<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ShowdownController extends Controller
{
    public function view(Request $request): InertiaResponse
    {
        return Inertia::render(
            'Showdown',
            [
            ],
        );
    }
}
