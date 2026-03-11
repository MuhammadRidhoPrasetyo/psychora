<?php

namespace App\Models\Psychotest;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PsychotestAspect extends Model
{
    use HasUuids;

    protected $fillable = [
        'code',
        'name',
        'description',
        'sort_order',
    ];

    public function characteristics(): HasMany
    {
        return $this->hasMany(PsychotestCharacteristic::class)->orderBy('sort_order');
    }
}
