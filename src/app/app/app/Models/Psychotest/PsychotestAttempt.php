<?php

namespace App\Models\Psychotest;

use App\Models\TestAttempt;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PsychotestAttempt extends Model
{
    use HasUuids;

    protected $fillable = [
        'test_attempt_id',
        'psychotest_form_id',
        'attempt_number',
        'status',
        'started_at',
        'submitted_at',
        'deadline_at',
        'score',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'deadline_at' => 'datetime',
            'score' => 'decimal:2',
        ];
    }

    public function testAttempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(PsychotestForm::class, 'psychotest_form_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(PsychotestAnswer::class);
    }

    public function resultCharacteristics(): HasMany
    {
        return $this->hasMany(PsychotestResultCharacteristic::class);
    }

    public function resultAspects(): HasMany
    {
        return $this->hasMany(PsychotestResultAspect::class);
    }
}
