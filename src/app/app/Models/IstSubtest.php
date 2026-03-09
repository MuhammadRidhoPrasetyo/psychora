<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class IstSubtest extends Model
{
    use HasUuids;

    protected $fillable = [
        'ist_form_id',
        'code',
        'name',
        'category',
        'duration_minutes',
        'sort_order',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(IstForm::class, 'ist_form_id');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'ist_subtest_questions')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }
}
