<?php

namespace App\Models\Inventory;

use App\Models\DataMaster\Product;
use App\Models\DataMaster\Warehouse;
use App\Models\Project\RecordingDepletion;
use App\Models\Project\RecordingEgg;
use App\Models\Project\RecordingStock;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductWarehouse extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'product_warehouses';

    protected $primaryKey = 'product_warehouse_id';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function stock_log()
    {
        return $this->hasMany(StockLog::class, 'product_warehouse_id');
    }

    /**
     * Get all of the recording_stock for the ProductWarehouse
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recording_stock()
    {
        return $this->hasMany(RecordingStock::class, 'product_warehouse_id');
    }

    public function recording_depletion()
    {
        return $this->hasMany(RecordingDepletion::class, 'product_warehouse_id');
    }

    public function recording_egg()
    {
        return $this->hasMany(RecordingEgg::class, 'product_warehouse_id');
    }

    public static function listProduct($productId = null)
    {
        $query = self::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->with([
                'product',
                'product.uom',
                'product.company',
                'product.product_category',
                'product.product_sub_category',
            ])
            ->groupBy('product_id');

        if ($productId) {
            $whereclause = [
                'product_id' => $productId,
            ];

            return $query->where($whereclause)->first();
        }

        return $query->get();
    }

    public static function listWarehouse()
    {
        $data = self::query();

        return $data;
    }
}
