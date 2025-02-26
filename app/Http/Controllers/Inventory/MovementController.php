<?php

namespace App\Http\Controllers\Inventory;

use App\Constants;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Nonstock;
use App\Models\DataMaster\Product;
use App\Models\DataMaster\Warehouse;
use App\Models\Expense\Expense;
use App\Models\Inventory\ProductWarehouse;
use App\Models\Inventory\StockAvailability;
use App\Models\Inventory\StockLog;
use App\Models\Inventory\StockMovement;
use App\Models\Inventory\StockMovementVehicle;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MovementController extends Controller
{
    private const VALIDATION_MESSAGES = [
        'origin_id.required'      => 'Gudang asal tidak boleh kosong',
        'destination_id.required' => 'Gudang tujuan tidak boleh kosong',
        'product_id.required'     => 'Produk tidak boleh kosong',
        'transfer_qty.required'   => 'Jumlah transfer tidak boleh kosong',
        'notes.required'          => 'Alasan transfer tidak boleh kosong',
    ];

    private const VALIDATION_RULES = [
        'origin_id'      => 'required',
        'destination_id' => 'required',
        'product_id'     => 'required',
        'transfer_qty'   => 'required|numeric|lte:current_stock',
        'notes'          => 'required',
    ];

    public function index(Request $req)
    {
        try {

            $param = [
                'title' => 'Persediaan > Transfer Stok',
                'data'  => StockMovement::with(['origin.location.company', 'destination', 'product', 'stock_movement_vehicle'])->get(),
            ];

            return view('inventory.movement.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => $e->getMessage(),
            ])->withInput();
        }
    }

    public function add(Request $req)
    {
        try {
            $param = [
                'title' => 'Persediaan > Transfer Stok > Tambah',
            ];

            if ($req->isMethod('post')) {
                $input     = $req->all();
                $validator = Validator::make($input, self::VALIDATION_RULES, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    if (isset($input['company_id'])) {
                        $input['company_name'] = Company::find($input['company_id'])->name;
                    }

                    if (isset($input['origin_id'])) {
                        $whOrigin = Warehouse::with(['location', 'location.area'])
                            ->find($input['origin_id']);

                        $input['origin_name']          = $whOrigin->name;
                        $input['origin_area_id']       = $whOrigin->location->area_id;
                        $input['origin_area_name']     = $whOrigin->location->area->name;
                        $input['origin_location_id']   = $whOrigin->location_id;
                        $input['origin_location_name'] = $whOrigin->location->name;
                    }

                    if (isset($input['destination_id'])) {
                        $whDestination = Warehouse::with(['location', 'location.area'])
                            ->find($input['destination_id']);

                        $input['destination_name']          = $whDestination->name;
                        $input['destination_area_id']       = $whDestination->location->area_id;
                        $input['destination_area_name']     = $whDestination->location->area->name;
                        $input['destination_location_id']   = $whDestination->location_id;
                        $input['destination_location_name'] = $whDestination->location->name;
                    }

                    if (isset($input['product_id'])) {
                        $input['product_name'] = Product::find($input['product_id'])->name;
                    }

                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                if (! isset($input['movement_vehicle'])) {
                    throw ValidationException::withMessages([
                        'movement_vehicle' => 'Armada angkut tidak boleh kosong',
                    ]);
                }

                DB::beginTransaction();
                $transferQty   = (int) str_replace('.', '', $input['transfer_qty']);
                $originId      = $input['origin_id'];
                $destinationId = $input['destination_id'];
                $productId     = $input['product_id'];
                $stockMovement = StockMovement::with(['origin.location.company', 'destination.location'])->create([
                    'origin_id'      => $originId,
                    'destination_id' => $destinationId,
                    'product_id'     => $productId,
                    'transfer_qty'   => $transferQty,
                    'notes'          => $input['notes'],
                    'created_by'     => auth()->user()->user_id,
                ]);

                $increment = str_pad($stockMovement->stock_movement_id, 5, '0', STR_PAD_LEFT);
                $alias     = $stockMovement->origin->location->company->alias ?? 'UNK';
                $mvNumber  = 'PND-'.$alias.'-'.$increment;
                $stockMovement->update(['movement_number' => $mvNumber]);

                $this->decreaseStock($productId, $originId, $destinationId, $transferQty, $input['notes']);

                $arrVehicle        = $input['movement_vehicle'] ?? [];
                $expenseMainPrices = [];
                foreach ($arrVehicle as $key => $value) {
                    $document = '';
                    if (isset($value['travel_document'])) {
                        $docUrl = FileHelper::upload($value['travel_document'], constants::INVENTORY_MOVEMENT);
                        if (! $docUrl['status']) {
                            DB::rollback();

                            return redirect()->back()->with('error', $docUrl['message'].' '.$value['travel_document'])->withInput();
                        }
                        $document = $docUrl['url'];
                    }

                    StockMovementVehicle::create([
                        'stock_movement_id'      => $stockMovement->stock_movement_id,
                        'supplier_id'            => $value['supplier_id'],
                        'vehicle_number'         => $value['vehicle_number'],
                        'travel_document_number' => $value['travel_document_number'],
                        'travel_document'        => $document,
                        'transport_amount_item'  => str_replace('.', '', str_replace(',', '.', $value['transport_amount_item'])),
                        'transport_amount'       => str_replace('.', '', str_replace(',', '.', $value['transport_amount'])),
                        'driver_name'            => $value['driver_name'],
                    ]);
                    $nonstock            = Nonstock::where('name', 'like', '%ekspedisi%')->first();
                    $expenseMainPrices[] = [
                        'supplier_id' => $value['supplier_id'],
                        'nonstock_id' => $nonstock->nonstock_id,
                        'qty'         => $input['transfer_qty'],
                        'price'       => $value['transport_amount'],
                        'notes'       => 'Auto generated by system '.$mvNumber,
                    ];
                }

                $expenseEvent = Expense::expenseEvent([
                    'stock_movement_id'   => $stockMovement->stock_movement_id,
                    'location_id'         => $stockMovement->destination->location_id,
                    'kandangs'            => $stockMovement->destination->type === 2 ? [$stockMovement->destination->kandang_id] : [],
                    'trx_date'            => date('Y-m-d H:i'),
                    'expense_main_prices' => $expenseMainPrices,
                    'po_number'           => $mvNumber,
                ]);

                if (! $expenseEvent['success']) {
                    \Log::info('expenseEvent purchase '.json_encode($expenseEvent));
                    DB::rollback();

                    return redirect()->back()->with('error', 'Terjadi masalah pada sistem, silahkan hubungi departemen IT');
                }

                DB::commit();
                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()->route('inventory.movement.index')->with($success);
            }

            return view('inventory.movement.add', $param);
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function decreaseStock($productId, $originId, $destinationId, $totalQty, $notes)
    {
        DB::transaction(function() use ($productId, $originId, $destinationId, $totalQty, $notes) {
            $arrWarehouseTf = [$originId, $destinationId];
            for ($i = 0; $i < 2; $i++) {
                $triggerStock = StockLog::triggerStock([
                    'product_id'   => $productId,
                    'stock_date'   => date('Y-m-d'),
                    'warehouse_id' => $arrWarehouseTf[$i],
                    'decrease'     => $i === 0 ? $totalQty : 0,
                    'increase'     => $i === 1 ? $totalQty : 0,
                    'stocked_by'   => 'Transfer Stok',
                    'notes'        => $notes,
                ]);
            }

            $originPW = ProductWarehouse::where([
                'product_id'   => $productId,
                'warehouse_id' => $originId,
            ])->first();

            // Fetch origin stock in FIFO order
            $originAvailabilities = StockAvailability::where('product_warehouse_id', $originPW->product_warehouse_id)
                ->orderBy('stock_availability_id', 'asc')
                ->get();

            $remainingQty = $totalQty;
            foreach ($originAvailabilities as $stock) {
                if ($remainingQty <= 0) {
                    break;
                } // Exit when transfer is complete

                $currentQty  = $stock->current_qty;
                $transferQty = min($remainingQty, $currentQty); // Determine how much to transfer

                // Deduct from current stock
                $stock->update(['current_qty' => $currentQty - $transferQty]);
                $destinationPW = ProductWarehouse::where([
                    'product_id'   => $productId,
                    'warehouse_id' => $destinationId,
                ])->first();

                // Create stock entry in the destination warehouse
                StockAvailability::create([
                    'product_warehouse_id'       => $destinationPW->product_warehouse_id,
                    'current_qty'                => $transferQty,
                    'product_price'              => $stock->product_price,
                    'received_date'              => $stock->received_date,
                    'purchase_item_id'           => $stock->purchase_item_id,
                    'purchase_item_reception_id' => $stock->purchase_item_reception_id,
                ]);

                $remainingQty -= $transferQty; // Deduct transferred quantity
            }
        });
    }

    public function detail(Request $req)
    {
        try {
            $param = [
                'title' => 'Persediaan > Transfer Stok > Detail',
                'data'  => StockMovement::with(['product', 'origin.location.company', 'destination', 'stock_movement_vehicle'])->find($req->id),
            ];

            return view('inventory.movement.detail', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
