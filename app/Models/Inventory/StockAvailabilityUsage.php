<?php

namespace App\Models\Inventory;

use App\Models\Marketing\MarketingProduct;
use App\Models\Project\Recording;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAvailabilityUsage extends Model
{
    use HasFactory;

    protected $table = 'stock_availability_usages';

    protected $primaryKey = 'stock_availability_usage_id';

    public $timestamps = false;

    protected $fillable = [
        'stock_availability_id',
        'stock_movement_id',
        'recording_id',
        'marketing_product_id',
        'usage_qty',
    ];

    /**
     * Get the stock_avalability that owns the StockAvailabilityUsage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock_avalability()
    {
        return $this->belongsTo(StockAvailability::class, 'stock_availability_id');
    }

    /**
     * Get the stock_movement that owns the StockAvailabilityUsage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock_movement()
    {
        return $this->belongsTo(StockMovement::class, 'stock_movement_id');
    }

    /**
     * Get the recording that owns the StockAvailabilityUsage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recording()
    {
        return $this->belongsTo(Recording::class, 'recording_id');
    }

    /**
     * Get the marketing_product that owns the StockAvailabilityUsage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function marketing_product()
    {
        return $this->belongsTo(MarketingProduct::class, 'marketing_product_id');
    }
}
