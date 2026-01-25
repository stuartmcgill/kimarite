<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ShowdownWrestler;
use App\Models\ShowdownWrestlerCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ShowdownController extends Controller
{
    public function view(Request $request): InertiaResponse
    {
        $categories = [
            [
                'code' => 'weight',
                'name' => 'Weight',
                'suffix' => 'kg',
                'inverse' => false,
            ],
            [
                'code' => 'yusho',
                'name' => 'Yusho',
                'suffix' => '',
                'inverse' => false,
            ],
            [
                'code' => 'prizes',
                'name' => 'Prizes',
                'suffix' => '',
                'inverse' => false,
            ],
            [
                'code' => 'bouts',
                'name' => 'Bouts',
                'suffix' => '',
                'inverse' => false,
            ],
            [
                'code' => 'kyujo',
                'name' => 'Kyujo',
                'suffix' => '%',
                'inverse' => true,
            ],
            [
                'code' => 'kimarite',
                'name' => 'Kimarite',
                'suffix' => 'KV50',
                'inverse' => false,
            ],
        ];

        $cards = $this->fetchCards();

        return Inertia::render(
            'Showdown',
            [
                'game' => [
                    'name' => 'Sumo showdown!',
                    'categories' => $categories,
                    'cards' => $cards,
                ],
            ],
        );
    }

    private function fetchCards(): array
    {
        $showdownWrestlers = ShowdownWrestler::all();

        return $showdownWrestlers->map(
            function (ShowdownWrestler $wrestler) {
                return [
                    'id' => $wrestler->nsk_id,
                    'name' => $wrestler->shikona_en,
                    'categories' => $wrestler->categories->map(fn (ShowdownWrestlerCategory $category) => [
                        'code' => $category->code,
                        'value' => $category->value,
                    ]),
                ];
            }
        )->toArray();
    }
}
