<?php

namespace App\Models\Psychotest;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychotestAnswer extends Model
{
    use HasUuids;

    protected $fillable = [
        'psychotest_attempt_id',
        'psychotest_question_id',
        'psychotest_option_id',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'answered_at' => 'datetime',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(PsychotestAttempt::class, 'psychotest_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(PsychotestQuestion::class, 'psychotest_question_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(PsychotestQuestionOption::class, 'psychotest_option_id');
    }
}
