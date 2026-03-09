<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KraepelinAnswer extends Model
{
    use HasUuids;

    protected $fillable = [
        'kraepelin_attempt_id',
        'kraepelin_form_column_id',
        'position',
        'top_number',
        'bottom_number',
        'user_answer',
        'correct_answer',
        'is_correct',
        'answered_at',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'answered_at' => 'datetime',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(KraepelinAttempt::class, 'kraepelin_attempt_id');
    }

    public function column(): BelongsTo
    {
        return $this->belongsTo(KraepelinFormColumn::class, 'kraepelin_form_column_id');
    }
}
