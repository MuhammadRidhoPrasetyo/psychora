<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IstResult extends Model
{
    use HasUuids;

    protected $fillable = [
        'ist_attempt_id',
        'category',
        'raw_score',
        'scaled_score',
        'percentile',
        'interpretation',
    ];

    protected function casts(): array
    {
        return [
            'raw_score' => 'decimal:2',
            'scaled_score' => 'decimal:2',
            'percentile' => 'decimal:2',
        ];
    }

    public function istAttempt(): BelongsTo
    {
        return $this->belongsTo(IstAttempt::class);
    }
}
