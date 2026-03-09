<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PapikostickAnswer extends Model
{
    use HasUuids;

    protected $fillable = [
        'papikostick_attempt_id',
        'papikostick_item_id',
        'selected_option_id',
        'score_value',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'answered_at' => 'datetime',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(PapikostickAttempt::class, 'papikostick_attempt_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(PapikostickItem::class, 'papikostick_item_id');
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(PapikostickItemOption::class, 'selected_option_id');
    }
}
