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
                'code' => 'cat_1',
                'name' => 'Category 1',
                'suffix' => '',
                'inverse' => false,
            ],
            [
                'code' => 'cat_2',
                'name' => 'Category 2',
                'suffix' => '',
                'inverse' => false,
            ],
            [
                'code' => 'cat_3',
                'name' => 'Category 3',
                'suffix' => '',
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
                        'code' => 'cat_1',
                        'value' => 10,
                    ],
                    [
                        'code' => 'cat_2',
                        'value' => 20,
                    ],
                    [
                        'code' => 'cat_3',
                        'value' => 30,
                    ],
                ],
            ],
            [
                'id' => 3842,
                'name' => 'Hoshoryu',
                'categories' => [
                    [
                        'code' => 'cat_1',
                        'value' => 20,
                    ],
                    [
                        'code' => 'cat_2',
                        'value' => 20,
                    ],
                    [
                        'code' => 'cat_3',
                        'value' => 20,
                    ],
                ],
            ],
            [
                'id' => 4175,
                'name' => 'Asahakuryu',
                'categories' => [
                    [
                        'code' => 'cat_1',
                        'value' => 30,
                    ],
                    [
                        'code' => 'cat_2',
                        'value' => 20,
                    ],
                    [
                        'code' => 'cat_3',
                        'value' => 10,
                    ],
                ],
            ],
            [
                'id' => 3616,
                'name' => 'Ura',
                'categories' => [
                    [
                        'code' => 'cat_1',
                        'value' => 1,
                    ],
                    [
                        'code' => 'cat_2',
                        'value' => 10,
                    ],
                    [
                        'code' => 'cat_3',
                        'value' => 100,
                    ],
                ],
            ],
        ];
    }
}
