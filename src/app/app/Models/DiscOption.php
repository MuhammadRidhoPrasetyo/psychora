<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscOption extends Model
{
    use HasUuids;

    protected $fillable = [
        'disc_question_id',
        'content',
        'disc_dimension',
        'sort_order',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(DiscQuestion::class, 'disc_question_id');
    }
}
