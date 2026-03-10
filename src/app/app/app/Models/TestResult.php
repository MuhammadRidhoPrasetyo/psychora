<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    use HasUuids;

    protected $fillable = [
        'test_attempt_id',
        'user_id',
        'test_id',
        'raw_score',
        'final_score',
        'percentage',
        'interpretation',
    ];

    protected function casts(): array
    {
        return [
            'raw_score' => 'decimal:2',
            'final_score' => 'decimal:2',
            'percentage' => 'decimal:2',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class, 'test_attempt_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }
}
