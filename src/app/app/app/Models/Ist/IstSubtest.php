<?php

namespace App\Models\Ist;

use App\Models\Question;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IstSubtest extends Model
{
    use HasUuids;

    protected $fillable = [
        'ist_form_id',
        'subtest_code',
        'subtest_name',
        'sort_order',
        'duration_minutes',
        'max_score',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(IstForm::class, 'ist_form_id');
    }

    public function formItem(): BelongsTo
    {
        return $this->belongsTo(IstFormItem::class, 'id', 'ist_subtest_id');
    }

    public function subtestQuestions(): HasMany
    {
        return $this->hasMany(IstSubtestQuestion::class)->orderBy('sort_order');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'ist_subtest_questions')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
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
