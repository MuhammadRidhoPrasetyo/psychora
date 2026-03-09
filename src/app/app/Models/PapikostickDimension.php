<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PapikostickDimension extends Model
{
    use HasUuids;

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PapikostickItem::class, 'dimension_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(PapikostickResult::class);
    }
}
