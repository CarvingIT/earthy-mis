<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'sku',
        'price',
        'description',
        'base_unit_id',
        'sales_unit_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function salesUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'sales_unit_id');
    }

    public function salesUnitRate(): float
    {
        $salesUnitQuantity = (float) ($this->salesUnit?->related_unit_quantity ?? 1);

        return round(((float) $this->price) * max($salesUnitQuantity, 1), 2);
    }

    /**
     * Convert sales unit quantity to base unit quantity
     * Example: 5 sacs × 25 kg/sac = 125 kg
     */
    public function convertSalesToBase(float $salesQuantity): float
    {
        $conversionFactor = (float) ($this->salesUnit?->related_unit_quantity ?? 1);
        return round($salesQuantity * $conversionFactor, 2);
    }

    /**
     * Convert base unit quantity to sales unit quantity
     * Example: 125 kg ÷ 25 kg/sac = 5 sacs
     */
    public function convertBaseToSales(float $baseQuantity): float
    {
        $conversionFactor = (float) ($this->salesUnit?->related_unit_quantity ?? 1);
        return round($baseQuantity / $conversionFactor, 2);
    }

    /**
     * Get current stock in base units (kg)
     */
    public function getCurrentStock(): float
    {
        return \App\Models\Stock::where('product_id', $this->id)->sum('quantity');
    }

    /**
     * Get current stock in sales units (bags/sacs)
     */
    public function getCurrentStockInSalesUnit(): float
    {
        $baseStock = $this->getCurrentStock();
        return $this->convertBaseToSales($baseStock);
    }
}
