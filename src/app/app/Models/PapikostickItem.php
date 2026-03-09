<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PapikostickItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'papikostick_form_id',
        'item_no',
        'statement',
        'dimension_id',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(PapikostickForm::class, 'papikostick_form_id');
    }

    public function dimension(): BelongsTo
    {
        return $this->belongsTo(PapikostickDimension::class, 'dimension_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(PapikostickItemOption::class)->orderBy('sort_order');
    }
}
