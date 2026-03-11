<?php

namespace App\Models\Ist;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IstFormItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'ist_form_id',
        'ist_subtest_id',
        'is_randomized',
        'number_of_questions',
        'sort_order',
        'minimum_score',
        'multiplier',
        'duration_minutes',
        'clue_first',
    ];

    protected function casts(): array
    {
        return [
            'is_randomized' => 'boolean',
            'clue_first' => 'boolean',
            'multiplier' => 'double',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(IstForm::class, 'ist_form_id');
    }

    public function subtest(): BelongsTo
    {
        return $this->belongsTo(IstSubtest::class, 'ist_subtest_id');
    }
}
