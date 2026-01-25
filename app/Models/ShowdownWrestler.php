<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShowdownWrestler extends Model
{
    protected $fillable = [
        'nsk_id',
        'sumodb_id',
        'sumoapi_id',
        'shikona_en',
        'shikona_jp',
        'shusshin',
        'stable',
        'rank',
    ];

    public function categories(): HasMany
    {
        return $this->hasMany(ShowdownWrestlerCategory::class, 'showdown_wrestler_id', 'id');
    }
}
