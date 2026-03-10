<?php

namespace App\Models\Psychotest;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychotestCharacteristicScore extends Model
{
    use HasUuids;

    protected $fillable = [
        'psychotest_characteristic_id',
        'score',
        'description',
    ];

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(PsychotestCharacteristic::class, 'psychotest_characteristic_id');
    }
}
