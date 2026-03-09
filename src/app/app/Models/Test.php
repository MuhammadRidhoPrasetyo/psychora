<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Test extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'program_id',
        'test_type_id',
        'title',
        'slug',
        'description',
        'instruction',
        'duration_minutes',
        'total_questions',
        'scoring_method',
        'visibility',
        'status',
        'created_by',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function testType(): BelongsTo
    {
        return $this->belongsTo(TestType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(TestSection::class)->orderBy('sort_order');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    public function discForm(): HasOne
    {
        return $this->hasOne(DiscForm::class);
    }

    public function istForm(): HasOne
    {
        return $this->hasOne(IstForm::class);
    }

    public function kraepelinForm(): HasOne
    {
        return $this->hasOne(KraepelinForm::class);
    }

    public function papikostickForm(): HasOne
    {
        return $this->hasOne(PapikostickForm::class);
    }

    public function cpnsBlueprints(): HasMany
    {
        return $this->hasMany(CpnsTestBlueprint::class);
    }
}
