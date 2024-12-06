<?php

namespace App\Models\Inventory;

use App\Models\Purchase\PurchaseItem;
use App\Models\UserManagement\User;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
    use HasFactory;

    protected $table = 'stock_logs';

    protected $primaryKey = 'stock_log_id';

    protected $fillable = [
        'product_warehouse_id',
        'stock_date',
        'increase',
        'decrease',
        'remaining_total',
        'stocked_by',
        'notes',
        'purchase_item_id',
        'created_by',
    ];

    public function stock()
    {
        return $this->belongsTo(ProductWarehouse::class, 'product_warehouse_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function purchase_item()
    {
        return $this->belongsTo(PurchaseItem::class, 'purchase_item_id');
    }

    public static function triggerStock($input)
    {
        try {
            DB::beginTransaction();
            $productId      = $input['product_id'];
            $warehouseId    = $input['warehouse_id'];
            $currentWhStock = ProductWarehouse::where(['product_id' => $productId, 'warehouse_id' => $warehouseId])->first();
            $increase       = str_replace(',', '', $input['increase'] ?? 0);
            $decrease       = str_replace(',', '', $input['decrease'] ?? 0);
            $stock          = new ProductWarehouse;

            if ($currentWhStock) {
                $currentQty = $currentWhStock->quantity;
                $qty        = $currentQty + $increase - $decrease;
                $currentWhStock->update([
                    'quantity' => $qty,
                ]);
                $stock->product_warehouse_id = $currentWhStock->product_warehouse_id;
            } else {
                $stock->product_id   = $productId;
                $stock->warehouse_id = $warehouseId;
                $stock->quantity     = $increase - $decrease;
                $stock->save();
            }
            $resultStock = ProductWarehouse::listProduct($productId);
            $stockLog    = self::create([
                'product_warehouse_id' => $stock->product_warehouse_id,
                'stock_date'           => $input['stock_date'] ?? date('Y-m-d'),
                'increase'             => $increase,
                'decrease'             => $decrease,
                'remaining_total'      => $resultStock->total_quantity,
                'stocked_by'           => $input['stocked_by'],
                'notes'                => $input['notes']            ?? null,
                'purchase_item_id'     => $input['purchase_item_id'] ?? null,
                'created_by'           => auth()->user()->user_id,
            ]);
            DB::commit();

            return [
                'result'  => true,
                'message' => 'Success update stock',
            ];
        } catch (\Exception $e) {
            DB::rollback();

            return [
                'result'  => false,
                'message' => $e->getMessage(),
            ];
        }

    }
}
