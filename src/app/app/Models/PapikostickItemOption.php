<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PapikostickItemOption extends Model
{
    use HasUuids;

    protected $fillable = [
        'papikostick_item_id',
        'label',
        'score_value',
        'sort_order',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(PapikostickItem::class, 'papikostick_item_id');
    }
}
