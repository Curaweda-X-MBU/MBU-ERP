<?php

namespace App\Models\Purchase;

use App\Models\DataMaster\Product;
use App\Models\DataMaster\Warehouse;
use App\Models\Inventory\StockAvailability;
use App\Models\Inventory\StockLog;
use App\Models\Project\Project;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_items';

    protected $primaryKey = 'purchase_item_id';

    public $timestamps = false;

    protected $fillable = [
        'purchase_id',
        'product_id',
        // 'warehouse_id',
        // 'project_id',
        'qty',
        'price',
        'tax',
        'discount',
        'total',
        'total_not_received',
        'amount_not_received',
        'total_received',
        'amount_received',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // public function project()
    // {
    //     return $this->belongsTo(Project::class, 'project_id');
    // }

    // public function warehouse()
    // {
    //     return $this->belongsTo(Warehouse::class, 'warehouse_id');
    // }

    public function purchase_item_reception()
    {
        return $this->hasMany(PurchaseItemReception::class, 'purchase_item_id');
    }

    public function stock_log()
    {
        return $this->hasMany(StockLog::class, 'purchase_item_id');
    }

    public function stock_availability()
    {
        return $this->hasMany(StockAvailability::class, 'purchase_item_id');
    }

    public function purchase_item_alocation()
    {
        return $this->hasMany(PurchaseItemAlocation::class, 'purchase_item_id');
    }
}
