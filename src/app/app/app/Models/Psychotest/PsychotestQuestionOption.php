<?php

namespace App\Models\Psychotest;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PsychotestQuestionOption extends Model
{
    use HasUuids;

    protected $fillable = [
        'psychotest_question_id',
        'label',
        'statement',
        'sort_order',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(PsychotestQuestion::class, 'psychotest_question_id');
    }

    public function characteristicMappings(): HasMany
    {
        return $this->hasMany(PsychotestOptionCharacteristicMapping::class, 'psychotest_option_id');
    }
}
