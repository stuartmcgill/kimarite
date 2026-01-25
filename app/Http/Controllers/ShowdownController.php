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
        return [
            [
                'id' => 3622,
                'name' => 'Kirishima',
                'categories' => [
                    [
                        'code' => 'weight',
                        'value' => 10,
                    ],
                    [
                        'code' => 'yusho',
                        'value' => 20,
                    ],
                    [
                        'code' => 'prizes',
                        'value' => 30,
                    ],
                ],
            ],
            [
                'id' => 3842,
                'name' => 'Hoshoryu',
                'categories' => [
                    [
                        'code' => 'weight',
                        'value' => 20,
                    ],
                    [
                        'code' => 'yusho',
                        'value' => 20,
                    ],
                    [
                        'code' => 'prizes',
                        'value' => 20,
                    ],
                ],
            ],
            [
                'id' => 4175,
                'name' => 'Asahakuryu',
                'categories' => [
                    [
                        'code' => 'weight',
                        'value' => 30,
                    ],
                    [
                        'code' => 'yusho',
                        'value' => 20,
                    ],
                    [
                        'code' => 'prizes',
                        'value' => 10,
                    ],
                ],
            ],
            [
                'id' => 3616,
                'name' => 'Ura',
                'categories' => [
                    [
                        'code' => 'weight',
                        'value' => 1,
                    ],
                    [
                        'code' => 'yusho',
                        'value' => 10,
                    ],
                    [
                        'code' => 'prizes',
                        'value' => 100,
                    ],
                ],
            ],
        ];
    }
}
