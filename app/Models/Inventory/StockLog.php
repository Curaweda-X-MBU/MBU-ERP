<?php

namespace App\Models\Inventory;

use App\Helpers\Parser;
use App\Models\Project\RecordingStock;
use App\Models\Purchase\PurchaseItem;
use App\Models\Purchase\PurchaseItemReception;
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
        // 'project_id',
        // egg_recording,
        // chick_recording
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
            $increase       = Parser::parseLocale($input['increase'] ?? 0);
            $decrease       = Parser::parseLocale($input['decrease'] ?? 0);
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

            if (isset($input['purchase_item_id']) && isset($input['purchase_item_reception_id'])) {
                $purchaseReception = PurchaseItemReception::with('purchase_item')
                    ->find($input['purchase_item_reception_id']);

                StockAvailability::create([
                    'product_warehouse_id'       => $stock->product_warehouse_id,
                    'current_qty'                => $purchaseReception->total_received,
                    'product_price'              => ($purchaseReception->purchase_item->price) + ($purchaseReception->purchase_item->price * $purchaseReception->purchase_item->tax / 100),
                    'received_date'              => $purchaseReception->received_date,
                    'purchase_item_id'           => $input['purchase_item_id'],
                    'purchase_item_reception_id' => $input['purchase_item_reception_id'],
                ]);
            }

            if (isset($input['recording_stock_id'])) {
                $originAvailabilities = StockAvailability::where('product_warehouse_id', $stock->product_warehouse_id)
                    ->orderBy('stock_availability_id', 'asc')
                    ->get();

                $remainingQty = $decrease;
                $usageAmount  = 0;
                foreach ($originAvailabilities as $val) {
                    if ($remainingQty <= 0) {
                        break;
                    }
                    $currentQty = $val->current_qty;
                    $usageQty   = min($remainingQty, $currentQty);

                    $val->update(['current_qty' => $currentQty - $usageQty]);
                    $destinationPW = ProductWarehouse::where([
                        'product_id'   => $productId,
                        'warehouse_id' => $warehouseId,
                    ])->first();

                    StockAvailability::create([
                        'product_warehouse_id'       => $destinationPW->product_warehouse_id,
                        'current_qty'                => $usageQty,
                        'product_price'              => $val->product_price,
                        'received_date'              => $val->received_date,
                        'purchase_item_id'           => $val->purchase_item_id,
                        'purchase_item_reception_id' => $val->purchase_item_reception_id,
                        'recording_stock_id'         => $input['recording_stock_id'],
                    ]);

                    $remainingQty -= $usageQty;
                    $usageAmount += $usageQty * $val->product_price;
                }
                $recordingStock = RecordingStock::find($input['recording_stock_id']);
                $recordingStock->update(['usage_amount' => $usageAmount]);
            }

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
