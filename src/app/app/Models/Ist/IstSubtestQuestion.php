<?php

namespace App\Models\Ist;

use App\Models\Question;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IstSubtestQuestion extends Model
{
    use HasUuids;

    protected $fillable = [
        'ist_subtest_id',
        'question_id',
        'sort_order',
    ];

    public function subtest(): BelongsTo
    {
        return $this->belongsTo(IstSubtest::class, 'ist_subtest_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
