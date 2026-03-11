<?php

namespace App\Models\Disc;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscResult extends Model
{
    use HasUuids;

    protected $fillable = [
        'disc_attempt_id',
        'most_d', 'most_i', 'most_s', 'most_c', 'most_star',
        'least_d', 'least_i', 'least_s', 'least_c', 'least_star',
        'score_d', 'score_i', 'score_s', 'score_c',
        'dominant_profile',
        'interpretation',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(DiscAttempt::class, 'disc_attempt_id');
    }
}
