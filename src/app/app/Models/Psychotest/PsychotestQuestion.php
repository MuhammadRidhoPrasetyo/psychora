<?php

namespace App\Models\Psychotest;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PsychotestQuestion extends Model
{
    use HasUuids;

    protected $fillable = [
        'psychotest_form_id',
        'number',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(PsychotestForm::class, 'psychotest_form_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(PsychotestQuestionOption::class)->orderBy('sort_order');
    }
}
