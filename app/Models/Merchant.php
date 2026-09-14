<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'password',
        'currency',
        'timezone',
    ];

    protected $hidden = [
        'password',
    ];

    protected static function booted(): void
    {
        static::creating(function ($merchant) {
            if (empty($merchant->password)) {
                $merchant->password = \Illuminate\Support\Facades\Hash::make('123456');
            }
        });
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function dailyUsages(): HasMany
    {
        return $this->hasMany(DailyUsage::class);
    }

    public function usageEvents(): HasMany
    {
        return $this->hasMany(UsageEvent::class);
    }
}
