<?php

namespace App\Models\Psychotest;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychotestResultAspect extends Model
{
    use HasUuids;

    protected $fillable = [
        'psychotest_attempt_id',
        'psychotest_aspect_id',
        'raw_score',
        'scaled_score',
    ];

    protected function casts(): array
    {
        return [
            'raw_score' => 'float',
            'scaled_score' => 'float',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(PsychotestAttempt::class, 'psychotest_attempt_id');
    }

    public function aspect(): BelongsTo
    {
        return $this->belongsTo(PsychotestAspect::class, 'psychotest_aspect_id');
    }
}
