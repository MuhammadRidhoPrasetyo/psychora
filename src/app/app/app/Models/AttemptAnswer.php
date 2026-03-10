<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptAnswer extends Model
{
    use HasUuids;

    protected $fillable = [
        'test_attempt_id',
        'question_id',
        'selected_option_id',
        'answer_text',
        'answer_json',
        'is_correct',
        'score',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'answer_json' => 'array',
            'is_correct' => 'boolean',
            'score' => 'decimal:2',
            'answered_at' => 'datetime',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class, 'test_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }
}
