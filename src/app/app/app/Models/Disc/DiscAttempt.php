<?php

namespace App\Models\Disc;

use App\Models\TestAttempt;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DiscAttempt extends Model
{
    use HasUuids;

    protected $fillable = [
        'test_attempt_id',
        'disc_form_id',
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
        return $this->belongsTo(DiscForm::class, 'disc_form_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(DiscAnswer::class);
    }

    public function result(): HasOne
    {
        return $this->hasOne(DiscResult::class);
    }
}
