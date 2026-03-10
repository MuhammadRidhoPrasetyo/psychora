<?php

namespace App\Models\Kraepelin;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KraepelinAttemptColumn extends Model
{
    use HasUuids;

    protected $fillable = [
        'kraepelin_attempt_id',
        'column_number',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(KraepelinAttempt::class, 'kraepelin_attempt_id');
    }

    public function numbers(): HasMany
    {
        return $this->hasMany(KraepelinAttemptNumber::class)->orderBy('position');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(KraepelinAnswer::class, 'kraepelin_attempt_column_id');
    }
}
