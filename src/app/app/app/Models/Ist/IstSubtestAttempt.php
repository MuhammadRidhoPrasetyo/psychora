<?php

namespace App\Models\Ist;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IstSubtestAttempt extends Model
{
    use HasUuids;

    protected $fillable = [
        'ist_attempt_id',
        'ist_subtest_id',
        'subtest_code',
        'status',
        'started_at',
        'submitted_at',
        'deadline_at',
        'raw_score',
        'scaled_score',
        'random_seed',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'deadline_at' => 'datetime',
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

    public function answers(): HasMany
    {
        return $this->hasMany(IstAnswer::class);
    }
}
