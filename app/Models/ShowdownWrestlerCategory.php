<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ShowdownCategoryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShowdownWrestlerCategory extends Model
{
    protected $fillable = [
        'showdown_wrestler_id',
        'code',
        'value',
    ];

    protected $casts = [
        'code' => ShowdownCategoryType::class,
    ];

    public function showdownWrestler(): BelongsTo
    {
        return $this->belongsTo(ShowdownWrestler::class);
    }
}
