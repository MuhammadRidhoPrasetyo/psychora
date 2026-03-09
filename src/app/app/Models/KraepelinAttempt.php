<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KraepelinAttempt extends Model
{
    use HasUuids;

    protected $fillable = [
        'test_attempt_id',
        'kraepelin_form_id',
        'status',
        'started_at',
        'submitted_at',
        'total_answered',
        'total_correct',
        'total_wrong',
        'speed_score',
        'accuracy_score',
        'stability_score',
        'final_score',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'speed_score' => 'decimal:2',
            'accuracy_score' => 'decimal:2',
            'stability_score' => 'decimal:2',
            'final_score' => 'decimal:2',
        ];
    }

    public function testAttempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(KraepelinForm::class, 'kraepelin_form_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(KraepelinAnswer::class);
    }
}
