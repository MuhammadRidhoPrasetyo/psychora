<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PapikostickResult extends Model
{
    use HasUuids;

    protected $fillable = [
        'papikostick_attempt_id',
        'papikostick_dimension_id',
        'raw_score',
        'normalized_score',
        'interpretation',
    ];

    protected function casts(): array
    {
        return [
            'raw_score' => 'decimal:2',
            'normalized_score' => 'decimal:2',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(PapikostickAttempt::class, 'papikostick_attempt_id');
    }

    public function dimension(): BelongsTo
    {
        return $this->belongsTo(PapikostickDimension::class, 'papikostick_dimension_id');
    }
}
