<?php

declare(strict_types=1);

namespace App\Enums;

enum ShowdownCategoryType: string
{
    case Weight = 'weight';
    case Yusho = 'yusho';
    case Prizes = 'prizes';
    case Bouts = 'bouts';
    case Kyujo = 'kyujo';
    case Kimarite = 'kimarite';
}
