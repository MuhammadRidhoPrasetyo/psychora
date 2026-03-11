<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionEssayAnswer extends Model
{
    use HasUuids;

    protected $fillable = [
        'question_id',
        'answer_text',
        'score',
        'match_type',
        'priority',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
