<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\ProductWarehouse;
use App\Models\Inventory\StockLog;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $req)
    {
        try {

            $param = [
                'title' => 'Persediaan > Produk',
                'data'  => ProductWarehouse::listProduct(),
            ];

            return view('inventory.product.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => $e->getMessage(),
            ])->withInput();
        }
    }

    public function detail(Request $req)
    {
        try {
            $data = ProductWarehouse::with([
                'warehouse',
                'product',
                'product.uom',
                'product.company',
                'product.product_category',
                'product.product_sub_category',
            ])->where('product_id', $req->id)->get();

            if (count($data) === 0) {
                return redirect()->back()->with('error', 'data tidak ditemukan')->withInput();
            }

            $productWhIds = $data->pluck('product_warehouse_id')->toArray();
            $stockLog     = StockLog::with('stock')
                ->whereIn('product_warehouse_id', $productWhIds)
                ->orderBy('created_at', 'DESC')
                ->get();
            $param = [
                'title'          => 'Persediaan > Produk > Detail',
                'data'           => $data,
                'total_quantity' => $data->sum('quantity'),
                'stock_log'      => $stockLog,
            ];

            return view('inventory.product.detail', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function checkStockByWarehouse(Request $req)
    {
        $data = ProductWarehouse::where([
            'product_id'   => $req->product_id,
            'warehouse_id' => $req->warehouse_id,
        ])->first();

        $qty = $data->quantity ?? 0;

        return $qty;
    }
}
