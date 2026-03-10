<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestPackage extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'program_id',
        'code',
        'name',
        'description',
        'is_premium',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TestPackageItem::class)->orderBy('sort_order');
    }

    public function tests(): BelongsToMany
    {
        return $this->belongsToMany(Test::class, 'test_package_items')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }
}
