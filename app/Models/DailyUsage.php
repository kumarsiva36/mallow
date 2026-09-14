<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'customer_id',
        'metric',
        'usage_date',
        'total_units',
        'event_count',
        'last_recorded_at',
    ];

    protected $casts = [
        'usage_date' => 'date:Y-m-d',
        'total_units' => 'integer',
        'event_count' => 'integer',
        'last_recorded_at' => 'datetime',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
