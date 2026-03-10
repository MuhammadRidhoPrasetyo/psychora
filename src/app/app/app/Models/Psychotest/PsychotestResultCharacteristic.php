<?php

namespace App\Models\Psychotest;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychotestResultCharacteristic extends Model
{
    use HasUuids;

    protected $fillable = [
        'psychotest_attempt_id',
        'psychotest_characteristic_id',
        'raw_score',
        'scaled_score',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(PsychotestAttempt::class, 'psychotest_attempt_id');
    }

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(PsychotestCharacteristic::class, 'psychotest_characteristic_id');
    }
}
