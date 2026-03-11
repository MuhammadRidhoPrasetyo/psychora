<?php

namespace App\Models\Psychotest;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PsychotestCharacteristic extends Model
{
    use HasUuids;

    protected $fillable = [
        'psychotest_aspect_id',
        'code',
        'name',
        'description',
        'sort_order',
    ];

    public function aspect(): BelongsTo
    {
        return $this->belongsTo(PsychotestAspect::class, 'psychotest_aspect_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(PsychotestCharacteristicScore::class)->orderBy('score');
    }
}
