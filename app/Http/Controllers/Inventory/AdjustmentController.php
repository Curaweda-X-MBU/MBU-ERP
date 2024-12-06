<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Inventory\ProductWarehouse;
use App\Models\Inventory\StockLog;
use App\Models\DataMaster\Product;
use App\Models\DataMaster\Warehouse;

use DB;

class AdjustmentController extends Controller
{
    private const VALIDATION_MESSAGES = [
        'product_id.required' => 'Produk tidak boleh kosong',
        'warehouse_id.required' => 'Gudang / tempat penyimpanan tidak boleh kosong',
        'increase.required' => 'Penambahan stok harus diisi',
        'increase.min' => 'Penambahan stok minimal 0',
        'decrease.required' => 'Pengurangan stok harus diisi',
        'decrease.min' => 'Pengurangan stok minimal 0',
    ];

    private const VALIDATION_RULES = [
        'product_id' => 'required',
        'warehouse_id' => 'required',
        'increase' => 'required|min:0',
        'decrease' => 'required|min:0',
    ];

    public function index(Request $req) {
        try {
            
            $param = [
                'title' => 'Persediaan > Penyesuaian Stok',
                'data' => StockLog::with([
                    'stock',
                    'stock.product',
                    'stock.warehouse'
                ])
                ->whereIn('stocked_by', ['Tambah Data', 'Penyesuaian'])
                ->get()
            ];
            return view('inventory.adjustment.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => $e->getMessage()
            ])->withInput();
        }
    }

    public function add(Request $req) {
        try {
            $param = [
                'title' => 'Persediaan > Penyesuaian Stok > Tambah',
            ];
            
            if ($req->isMethod('post')) {
                $input = $req->all();
                $validator = Validator::make($input, self::VALIDATION_RULES, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    if(isset($input['product_id'])) {
                        $input['product_name'] = Product::find($input['product_id'])->name;
                    }
                    if(isset($input['warehouse_id'])) {
                        $input['warehouse_name'] = Warehouse::find($input['warehouse_id'])->name;
                    }
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }
                $input['stocked_by'] = 'Penyesuaian';
                $input['stock_date'] = date('Y-m-d');
                $triggerStock = StockLog::triggerStock($input);
                if (!$triggerStock['result']) {
                    return redirect()->back()->with('error', $triggerStock['message']);
                }

                $success = ['success' => 'Data Berhasil disimpan'];
                return redirect()->route('inventory.adjustment.index')->with($success);
            }

            return view('inventory.adjustment.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
 }
