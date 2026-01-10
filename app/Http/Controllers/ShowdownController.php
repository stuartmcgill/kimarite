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

        $cards = [
            [
                'name' => 'Person 1',
                'photoUrl' => '',
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
                'name' => 'Person 2',
                'photoUrl' => '',
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
                'name' => 'Person 3',
                'photoUrl' => '',
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
        ];

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
}
