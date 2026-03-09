<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KraepelinFormColumn extends Model
{
    use HasUuids;

    protected $fillable = [
        'kraepelin_form_id',
        'column_number',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(KraepelinForm::class, 'kraepelin_form_id');
    }

    public function numbers(): HasMany
    {
        return $this->hasMany(KraepelinFormNumber::class)->orderBy('position');
    }
}
