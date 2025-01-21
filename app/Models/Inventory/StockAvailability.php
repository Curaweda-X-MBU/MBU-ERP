<?php

namespace App\Models\Inventory;

use App\Models\Project\RecordingStock;
use App\Models\Purchase\PurchaseItem;
use App\Models\Purchase\PurchaseItemReception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAvailability extends Model
{
    use HasFactory;

    protected $table = 'stock_availabilities';

    protected $primaryKey = 'stock_availability_id';

    public $timestamps = false;

    protected $fillable = [
        'product_warehouse_id',
        'current_qty',
        'product_price',
        'received_date',
        'purchase_item_id',
        'purchase_item_reception_id',
        'recording_stock_id',
    ];

    /**
     * Get the product_warehouse that owns the StockAvailability
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product_warehouse()
    {
        return $this->belongsTo(ProductWarehouse::class, 'product_warehouse_id');
    }

    /**
     * Get the purchase_item that owns the StockAvailability
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase_item()
    {
        return $this->belongsTo(PurchaseItem::class, 'purchase_item_id');
    }

    public function purchase_item_reception()
    {
        return $this->belongsTo(PurchaseItemReception::class, 'purchase_item_reception_id');
    }

    public function recording_stock()
    {
        return $this->belongsTo(RecordingStock::class, 'recording_stock_id');
    }
}
