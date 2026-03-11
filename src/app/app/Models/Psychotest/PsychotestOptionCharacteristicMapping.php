<?php

namespace App\Models\Psychotest;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PsychotestOptionCharacteristicMapping extends Model
{
    use HasUuids;

    protected $table = 'psychotest_option_characteristic_mappings';

    protected $fillable = [
        'psychotest_option_id',
        'psychotest_aspect_id',
        'psychotest_characteristic_id',
        'weight',
    ];

    public function option(): BelongsTo
    {
        return $this->belongsTo(PsychotestQuestionOption::class, 'psychotest_option_id');
    }

    public function aspect(): BelongsTo
    {
        return $this->belongsTo(PsychotestAspect::class, 'psychotest_aspect_id');
    }

    public function characteristic(): BelongsTo
    {
        return $this->belongsTo(PsychotestCharacteristic::class, 'psychotest_characteristic_id');
    }
}
