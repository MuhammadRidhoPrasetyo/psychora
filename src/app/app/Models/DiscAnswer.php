<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscAnswer extends Model
{
    use HasUuids;

    protected $fillable = [
        'disc_attempt_id',
        'disc_question_id',
        'most_option_id',
        'least_option_id',
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
        return $this->belongsTo(DiscAttempt::class, 'disc_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(DiscQuestion::class, 'disc_question_id');
    }

    public function mostOption(): BelongsTo
    {
        return $this->belongsTo(DiscOption::class, 'most_option_id');
    }

    public function leastOption(): BelongsTo
    {
        return $this->belongsTo(DiscOption::class, 'least_option_id');
    }
}
