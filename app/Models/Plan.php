<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_category_id',
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'price_lifetime',
        'features',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'price_lifetime' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the service category
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    /**
     * Get all orders for this plan
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope: only active plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }

    /**
     * Scope: order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Get price for billing cycle
     */
    public function getPrice($billingCycle = 'monthly')
    {
        return match ($billingCycle) {
            'yearly' => $this->price_yearly ?? $this->price_monthly * 12,
            'lifetime' => $this->price_lifetime,
            default => $this->price_monthly,
        };
    }
}
