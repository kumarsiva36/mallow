<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'customer_id',
        'plan_id',
        'status',
        'starts_at',
        'current_cycle_start',
        'current_cycle_end',
        'cancelled_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'current_cycle_start' => 'datetime',
        'current_cycle_end' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function segments(): HasMany
    {
        return $this->hasMany(SubscriptionSegment::class)->orderBy('starts_at', 'asc');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isDueForBilling(?Carbon $asOf = null): bool
    {
        $now = $asOf ?? Carbon::now();
        return $this->isActive() && $this->current_cycle_end->lte($now);
    }
}
