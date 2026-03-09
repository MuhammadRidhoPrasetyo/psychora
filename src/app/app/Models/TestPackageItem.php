<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TestPackageItem extends Pivot
{
    use HasUuids;

    protected $table = 'test_package_items';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'test_package_id',
        'test_id',
        'sort_order',
    ];

    public function testPackage(): BelongsTo
    {
        return $this->belongsTo(TestPackage::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }
}
