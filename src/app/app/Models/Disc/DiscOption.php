<?php

namespace App\Models\Disc;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscOption extends Model
{
    use HasUuids;

    protected $fillable = [
        'disc_question_id',
        'option_text',
        'sort_order',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(DiscQuestion::class, 'disc_question_id');
    }

    public function scorings(): HasMany
    {
        return $this->hasMany(DiscOptionScoring::class);
    }
}
