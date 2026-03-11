<?php

namespace App\Models\Kraepelin;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KraepelinAttemptNumber extends Model
{
    use HasUuids;

    protected $fillable = [
        'kraepelin_attempt_column_id',
        'position',
        'value',
    ];

    public function column(): BelongsTo
    {
        return $this->belongsTo(KraepelinAttemptColumn::class, 'kraepelin_attempt_column_id');
    }
}
