<?php

namespace App\Models\Kraepelin;

use App\Models\TestAttempt;
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
        'numbers_per_column',
        'columns_count',
        'duration_per_column',
        'total_answered',
        'total_correct',
        'total_wrong',
        'total_skipped',
        'speed_score',
        'accuracy_score',
        'stability_score',
        'final_score',
        'attempt_number',
        'status',
        'started_at',
        'submitted_at',
        'deadline_at',
    ];

    protected function casts(): array
    {
        return [
            'speed_score' => 'decimal:2',
            'accuracy_score' => 'decimal:2',
            'stability_score' => 'decimal:2',
            'final_score' => 'decimal:2',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'deadline_at' => 'datetime',
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

    public function columns(): HasMany
    {
        return $this->hasMany(KraepelinAttemptColumn::class)->orderBy('column_number');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(KraepelinAnswer::class);
    }
}
