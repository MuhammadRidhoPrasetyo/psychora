<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProgramTestType extends Pivot
{
    use HasUuids;

    protected $table = 'program_test_types';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'program_id',
        'test_type_id',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function testType(): BelongsTo
    {
        return $this->belongsTo(TestType::class);
    }
}
