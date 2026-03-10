<?php

namespace App\Models\Kraepelin;

use App\Models\Test;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KraepelinForm extends Model
{
    use HasUuids;

    protected $fillable = [
        'test_id',
        'title',
        'description',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }
}
