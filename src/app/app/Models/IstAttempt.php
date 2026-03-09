<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IstAttempt extends Model
{
    use HasUuids;

    protected $fillable = [
        'test_attempt_id',
        'ist_form_id',
        'current_subtest_code',
        'status',
        'started_at',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    public function testAttempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(IstForm::class, 'ist_form_id');
    }

    public function subtestAttempts(): HasMany
    {
        return $this->hasMany(IstSubtestAttempt::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(IstResult::class);
    }
}
