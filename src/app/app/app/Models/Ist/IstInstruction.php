<?php

namespace App\Models\Ist;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IstInstruction extends Model
{
    use HasUuids;

    protected $fillable = [
        'ist_form_id',
        'ist_subtest_id',
        'title',
        'content',
        'sort_order',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(IstForm::class, 'ist_form_id');
    }

    public function subtest(): BelongsTo
    {
        return $this->belongsTo(IstSubtest::class, 'ist_subtest_id');
    }
}
