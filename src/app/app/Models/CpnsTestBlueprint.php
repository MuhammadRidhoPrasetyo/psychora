<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CpnsTestBlueprint extends Model
{
    use HasUuids;

    protected $fillable = [
        'test_id',
        'category_code',
        'total_questions',
        'passing_score',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }
}
