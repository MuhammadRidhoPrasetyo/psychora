<?php

namespace App\Models\Ist;

use App\Models\Test;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IstForm extends Model
{
    use HasUuids;

    protected $fillable = [
        'test_id',
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

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function subtests(): HasMany
    {
        return $this->hasMany(IstSubtest::class)->orderBy('sort_order');
    }

    public function formItems(): HasMany
    {
        return $this->hasMany(IstFormItem::class)->orderBy('sort_order');
    }

    public function instructions(): HasMany
    {
        return $this->hasMany(IstInstruction::class)->orderBy('sort_order');
    }

    public function clues(): HasMany
    {
        return $this->hasMany(IstClue::class);
    }
}
