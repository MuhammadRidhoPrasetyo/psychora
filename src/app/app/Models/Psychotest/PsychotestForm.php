<?php

namespace App\Models\Psychotest;

use App\Models\Test;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PsychotestForm extends Model
{
    use HasUuids;

    protected $fillable = [
        'test_id',
        'name',
        'description',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(PsychotestQuestion::class)->orderBy('number');
    }
}
