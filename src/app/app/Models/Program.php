<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasUuids;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function testTypes(): BelongsToMany
    {
        return $this->belongsToMany(TestType::class, 'program_test_types')
            ->using(ProgramTestType::class)
            ->withTimestamps();
    }

    public function tests(): HasMany
    {
        return $this->hasMany(Test::class);
    }

    public function testPackages(): HasMany
    {
        return $this->hasMany(TestPackage::class);
    }
}
