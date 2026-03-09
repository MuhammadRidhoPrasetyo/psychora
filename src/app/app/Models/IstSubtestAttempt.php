<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IstSubtestAttempt extends Model
{
    use HasUuids;

    protected $fillable = [
        'ist_attempt_id',
        'ist_subtest_id',
        'started_at',
        'submitted_at',
        'raw_score',
        'scaled_score',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'raw_score' => 'decimal:2',
            'scaled_score' => 'decimal:2',
        ];
    }

    public function istAttempt(): BelongsTo
    {
        return $this->belongsTo(IstAttempt::class);
    }

    public function subtest(): BelongsTo
    {
        return $this->belongsTo(IstSubtest::class, 'ist_subtest_id');
    }
}
