<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PapikostickAttempt extends Model
{
    use HasUuids;

    protected $fillable = [
        'test_attempt_id',
        'papikostick_form_id',
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
        return $this->belongsTo(PapikostickForm::class, 'papikostick_form_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(PapikostickAnswer::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(PapikostickResult::class);
    }
}
