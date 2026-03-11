<?php

namespace App\Models\Disc;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscOptionScoring extends Model
{
    use HasUuids;

    protected $fillable = [
        'disc_option_id',
        'response_type',
        'disc_code',
        'score_value',
    ];

    public function option(): BelongsTo
    {
        return $this->belongsTo(DiscOption::class, 'disc_option_id');
    }
}
