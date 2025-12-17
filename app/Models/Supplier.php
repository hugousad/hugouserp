<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Supplier extends BaseModel
{
    use LogsActivity;

    protected ?string $moduleKey = 'suppliers';

    protected $fillable = ['branch_id', 'name', 'email', 'phone', 'address', 'tax_number', 'is_active', 'extra_attributes'];

    protected $casts = ['is_active' => 'bool', 'extra_attributes' => 'array'];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Supplier {$this->name} was {$eventName}");
    }
}
