<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscResult extends Model
{
    use HasUuids;

    protected $fillable = [
        'disc_attempt_id',
        'score_d',
        'score_i',
        'score_s',
        'score_c',
        'dominant_profile',
        'interpretation',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(DiscAttempt::class, 'disc_attempt_id');
    }
}
