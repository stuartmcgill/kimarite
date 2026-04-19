<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RikishiMatchModel extends Model
{
    protected $table = 'rikishi_matches';

    protected $fillable = [
        'basho_id',
        'division',
        'day',
        'east_id',
        'east_shikona',
        'east_rank',
        'west_id',
        'west_shikona',
        'west_rank',
        'kimarite',
        'winner_id',
        'winner_en',
        'winner_jp',
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    public function kimariteType(): BelongsTo
    {
        return $this->belongsTo(KimariteType::class, 'kimarite', 'name');
    }
}
