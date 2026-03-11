<?php

namespace App\Models\Cpns;

use App\Models\TestType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CpnsScoreRule extends Model
{
    use HasUuids;

    protected $fillable = [
        'test_type_id',
        'category_code',
        'correct_score',
        'wrong_score',
        'empty_score',
    ];

    protected function casts(): array
    {
        return [
            'correct_score' => 'decimal:2',
            'wrong_score' => 'decimal:2',
            'empty_score' => 'decimal:2',
        ];
    }

    public function testType(): BelongsTo
    {
        return $this->belongsTo(TestType::class);
    }
}
