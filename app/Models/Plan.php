<?php

namespace App\Models;

use App\Services\PlanCacheService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(function (Plan $plan) {
            app(PlanCacheService::class)->invalidate($plan);
        });

        static::deleted(function (Plan $plan) {
            app(PlanCacheService::class)->invalidate($plan);
        });
    }

    protected $fillable = [
        'merchant_id',
        'name',
        'code',
        'description',
        'base_price',
        'billing_cycle',
        'cycle_days',
        'included_usage_units',
        'overage_rate_per_unit',
        'prorate_allowance',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'cycle_days' => 'integer',
        'included_usage_units' => 'integer',
        'overage_rate_per_unit' => 'decimal:4',
        'prorate_allowance' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
