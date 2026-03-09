<?php

namespace App\Models;

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
        'status',
        'started_at',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
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
