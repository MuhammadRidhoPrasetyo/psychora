<?php

namespace App\Models\Disc;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscQuestion extends Model
{
    use HasUuids;

    protected $fillable = [
        'disc_form_id',
        'number',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(DiscForm::class, 'disc_form_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(DiscOption::class)->orderBy('sort_order');
    }
}
