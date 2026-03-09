<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TestAttempt extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'test_id',
        'attempt_no',
        'started_at',
        'submitted_at',
        'expired_at',
        'status',
        'total_score',
        'percentage',
        'result_payload',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'expired_at' => 'datetime',
            'total_score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'result_payload' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(TestResult::class);
    }

    public function discAttempt(): HasOne
    {
        return $this->hasOne(DiscAttempt::class);
    }

    public function istAttempt(): HasOne
    {
        return $this->hasOne(IstAttempt::class);
    }

    public function kraepelinAttempt(): HasOne
    {
        return $this->hasOne(KraepelinAttempt::class);
    }

    public function papikostickAttempt(): HasOne
    {
        return $this->hasOne(PapikostickAttempt::class);
    }
}
