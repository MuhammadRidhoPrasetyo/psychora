<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProgress extends Model
{
    use HasUuids;

    protected $table = 'user_progress';

    protected $fillable = [
        'user_id',
        'program_id',
        'test_type_id',
        'total_attempts',
        'average_score',
        'last_attempt_at',
    ];

    protected function casts(): array
    {
        return [
            'average_score' => 'decimal:2',
            'last_attempt_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function testType(): BelongsTo
    {
        return $this->belongsTo(TestType::class);
    }
}
