<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KraepelinForm extends Model
{
    use HasUuids;

    protected $fillable = [
        'test_id',
        'title',
        'description',
        'columns_count',
        'numbers_per_column',
        'duration_per_column_seconds',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function columns(): HasMany
    {
        return $this->hasMany(KraepelinFormColumn::class)->orderBy('column_number');
    }
}
