<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KraepelinFormNumber extends Model
{
    use HasUuids;

    protected $fillable = [
        'kraepelin_form_column_id',
        'position',
        'value',
    ];

    public function column(): BelongsTo
    {
        return $this->belongsTo(KraepelinFormColumn::class, 'kraepelin_form_column_id');
    }
}
