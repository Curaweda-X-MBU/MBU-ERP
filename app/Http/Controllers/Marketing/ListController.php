<?php

namespace App\Http\Controllers\Marketing;

use App\Constants;
use App\Helpers\FileHelper;
use App\Helpers\Parser;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Warehouse;
use App\Models\Inventory\ProductWarehouse;
use App\Models\Inventory\StockLog;
use App\Models\Marketing\Marketing;
use App\Models\Marketing\MarketingAdditPrice;
use App\Models\Marketing\MarketingDeliveryVehicle;
use App\Models\Marketing\MarketingProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = Marketing::with(['customer', 'company', 'marketing_payments'])
                ->whereNull('marketing_return_id')
                ->get();

            $param = [
                'title' => 'Penjualan > List',
                'data'  => $data,
            ];

            return view('marketing.list.index', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function add(Request $req)
    {
        try {
            $param = [
                'title' => 'Penjualan > Tambah',
            ];

            if ($req->isMethod('post')) {
                $input = $req->all();

                if (! $req->has('marketing_products')) {
                    return redirect()->back()->with('error', 'Produk Penjualan tidak boleh kosong')->withInput($input);
                }

                $success = DB::transaction(function() use ($req) {
                    $input            = $req->all();
                    $productPrice     = 0;
                    $additPrice       = 0;
                    $company          = Auth::user()->department->company;
                    $docReferencePath = '';

                    if (isset($input['doc_reference'])) {
                        $docUrl = FileHelper::upload($input['doc_reference'], constants::MARKETING_DOC_REFERENCE_PATH);
                        if (! $docUrl['status']) {
                            throw new \Exception($docUrl['message']);
                        }
                        $docReferencePath = $docUrl['url'];
                    }

                    $createdMarketing = Marketing::create([
                        'company_id'     => $company->company_id,
                        'customer_id'    => $input['customer_id'],
                        'sold_at'        => date('Y-m-d', strtotime($input['sold_at'])),
                        'doc_reference'  => $docReferencePath,
                        'notes'          => $input['notes'],
                        'sales_id'       => $input['sales_id'] ?? null,
                        'tax'            => $input['tax'],
                        'discount'       => Parser::parseLocale($input['discount']),
                        'payment_status' => array_search(
                            'Tempo',
                            Constants::MARKETING_PAYMENT_STATUS
                        ),
                        'marketing_status' => array_search(
                            'Diajukan',
                            Constants::MARKETING_STATUS
                        ),
                        'created_by' => Auth::id(),
                    ]);

                    $arrProduct = [];
                    if ($req->has('marketing_products')) {
                        $arrProduct = $req->input('marketing_products');

                        foreach ($arrProduct as $key => $value) {
                            $price       = Parser::parseLocale($value['price']);
                            $weightTotal = Parser::parseLocale($value['weight_total']);
                            $qty         = Parser::parseLocale($value['qty']);

                            $weightAvg  = $weightTotal / max($qty, 1);
                            $totalPrice = $weightTotal * $price;
                            $productPrice += $totalPrice;

                            $arrProduct[$key]['marketing_id'] = $createdMarketing->marketing_id;
                            $arrProduct[$key]['warehouse_id'] = $value['warehouse_id'];
                            $arrProduct[$key]['product_id']   = $value['product_id'];
                            $arrProduct[$key]['price']        = $price;
                            $arrProduct[$key]['weight_avg']   = $weightAvg;
                            $arrProduct[$key]['uom_id']       = $value['uom_id'];
                            $arrProduct[$key]['qty']          = $qty;
                            $arrProduct[$key]['weight_total'] = $weightTotal;
                            $arrProduct[$key]['total_price']  = $totalPrice;

                            // assign project_id
                            $project = Warehouse::find($value['warehouse_id'])->kandang->project()->where([
                                ['chickin_status', '=', 3],
                                ['project_status', '!=', 4],
                            ])->first() ?? null;

                            if ($project) {
                                $arrProduct[$key]['project_id'] = $project->project_id;
                            }
                        }

                        MarketingProduct::insert($arrProduct);
                    }

                    if ($req->has('marketing_addit_prices')) {
                        $arrPrice = $req->input('marketing_addit_prices');
                        $create   = false;
                        foreach ($arrPrice as $key => $value) {
                            $item  = $value['item'] ?? null;
                            $price = Parser::parseLocale($value['price'] ?? null);
                            $additPrice += $price;

                            if ($item && $price) {
                                $create                         = true;
                                $arrPrice[$key]['marketing_id'] = $createdMarketing->marketing_id;
                                $arrPrice[$key]['item']         = $item;
                                $arrPrice[$key]['price']        = $price;
                            }
                        }
                        if ($create) {
                            MarketingAdditPrice::insert($arrPrice);
                        }
                    }

                    $subTotal         = $productPrice;
                    $subTotalAfterTax = $productPrice + ($productPrice * ($input['tax'] / 100)) - Parser::parseLocale($input['discount']);

                    $createdMarketing->update([
                        'sub_total'   => $subTotal,
                        'grand_total' => $subTotalAfterTax + $additPrice,
                    ]);

                    $createdMarketing->update([
                        'id_marketing' => "DO.{$company->alias}.{$createdMarketing->marketing_id}",
                    ]);

                    // Success message according to project_id
                    if (! empty($arrProduct)) {
                        $projectIds       = array_column($arrProduct, 'project_id');
                        $projectIdsString = implode(', ', $projectIds);

                        return ['success' => "Penjualan Berhasil Disimpan | Terhubung Pada Project ID {$projectIdsString}"];
                    } else {
                        return ['success' => 'Penjualan Berhasil Disimpan | Tidak Terhubung Pada Project'];
                    }
                });

                return redirect()
                    ->route('marketing.list.index')
                    ->with($success);
            }

            return view('marketing.list.add', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function detail(Marketing $marketing)
    {
        try {
            $data = $marketing->load([
                'company',
                'customer',
                'sales',
                'marketing_products.warehouse',
                'marketing_products.product',
                'marketing_products.uom',
                'marketing_addit_prices',
                'marketing_delivery_vehicles.uom',
                'marketing_delivery_vehicles.sender',
            ]);
            $param = [
                'title' => 'Penjualan > Detail',
                'data'  => $data,
            ];

            return view('marketing.list.detail', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $req, Marketing $marketing)
    {
        try {
            $data  = $marketing->load(['company', 'customer', 'sales', 'marketing_products.warehouse', 'marketing_products.product', 'marketing_products.uom', 'marketing_addit_prices']);
            $param = [
                'title' => 'Penjualan > Edit',
                'data'  => $data,
            ];

            if ($req->isMethod('post')) {
                $input = $req->all();

                if (! $req->has('marketing_products')) {
                    return redirect()->back()->with('error', 'Produk Penjualan tidak boleh kosong')->withInput($input);
                }

                $success = DB::transaction(function() use ($req, $marketing) {
                    $input            = $req->all();
                    $productPrice     = 0;
                    $additPrice       = 0;
                    $existingDoc      = $marketing->doc_reference ?? null;
                    $docReferencePath = '';

                    if (isset($input['doc_reference'])) {
                        if ($existingDoc) {
                            FileHelper::delete($existingDoc);
                        }
                        $docUrl = FileHelper::upload($input['doc_reference'], constants::MARKETING_DOC_REFERENCE_PATH);
                        if (! $docUrl['status']) {
                            throw new \Exception($docUrl['message']);
                        }
                        $docReferencePath = $docUrl['url'];
                    } else {
                        $docReferencePath = $existingDoc;
                    }

                    $marketing->update([
                        'customer_id'   => $input['customer_id'],
                        'sold_at'       => date('Y-m-d', strtotime($input['sold_at'])),
                        'doc_reference' => $docReferencePath,
                        'notes'         => $input['notes'],
                        'sales_id'      => $input['sales_id'] ?? null,
                        'tax'           => $input['tax'],
                        'discount'      => Parser::parseLocale($input['discount']),
                    ]);

                    $arrProduct = [];
                    if ($req->has('marketing_products')) {
                        $marketing->marketing_products()->delete();
                        $arrProduct = $req->input('marketing_products');

                        foreach ($arrProduct as $key => $value) {
                            $price       = Parser::parseLocale($value['price']);
                            $weightTotal = Parser::parseLocale($value['weight_total']);
                            $qty         = Parser::parseLocale($value['qty']);

                            $weightAvg  = $weightTotal / max($qty, 1);
                            $totalPrice = $weightTotal * $price;
                            $productPrice += $totalPrice;

                            $arrProduct[$key]['marketing_id'] = $marketing->marketing_id;
                            $arrProduct[$key]['warehouse_id'] = $value['warehouse_id'];
                            $arrProduct[$key]['product_id']   = $value['product_id'];
                            $arrProduct[$key]['price']        = $price;
                            $arrProduct[$key]['weight_avg']   = $weightAvg;
                            $arrProduct[$key]['uom_id']       = $value['uom_id'];
                            $arrProduct[$key]['qty']          = $qty;
                            $arrProduct[$key]['weight_total'] = $weightTotal;
                            $arrProduct[$key]['total_price']  = $totalPrice;

                            // assign project_id
                            $project = Warehouse::find($value['warehouse_id'])->kandang->project()->where([
                                ['chickin_status', '=', 3],
                                ['project_status', '!=', 4],
                            ])->first() ?? null;

                            if ($project) {
                                $arrProduct[$key]['project_id'] = $project->project_id;
                            }
                        }

                        MarketingProduct::insert($arrProduct);
                    }

                    $marketing->marketing_addit_prices()->delete();
                    if ($req->has('marketing_addit_prices')) {
                        $arrPrice = $req->input('marketing_addit_prices');
                        $create   = false;
                        foreach ($arrPrice as $key => $value) {
                            $item  = $value['item'] ?? null;
                            $price = Parser::parseLocale($value['price'] ?? null);
                            $additPrice += $price;

                            if ($item && $price) {
                                $create                         = true;
                                $arrPrice[$key]['marketing_id'] = $marketing->marketing_id;
                                $arrPrice[$key]['item']         = $item;
                                $arrPrice[$key]['price']        = $price;
                            }
                        }
                        if ($create) {
                            MarketingAdditPrice::insert($arrPrice);
                        }
                    }

                    $subTotal         = $productPrice;
                    $subTotalAfterTax = $productPrice + ($productPrice * ($input['tax'] / 100)) - Parser::parseLocale($input['discount']);

                    $marketing->update([
                        'sub_total'   => $subTotal,
                        'grand_total' => $subTotalAfterTax + $additPrice,
                    ]);

                    // Success message according to project_id
                    if (! empty($arrProduct)) {
                        $projectIds       = array_column($arrProduct, 'project_id');
                        $projectIdsString = implode(', ', $projectIds);

                        return ['success' => "Penjualan Berhasil Disimpan | Terhubung Pada Project ID {$projectIdsString}"];
                    } else {
                        return ['success' => 'Penjualan Berhasil Disimpan | Tidak Terhubung Pada Project'];
                    }
                });

                return redirect()->route('marketing.list.detail', $marketing->marketing_id)->with($success);
            }

            return view('marketing.list.edit', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Marketing $marketing)
    {
        try {
            $marketing->delete();
            $success = ['success' => 'Data Berhasil dihapus'];

            return redirect()->route('marketing.list.index')->with($success);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Search the specified resource from storage.
     */
    public function realization(Request $req, Marketing $marketing)
    {
        try {
            $data = $marketing->load(['company', 'customer', 'sales', 'marketing_products.warehouse', 'marketing_products.product', 'marketing_products.uom', 'marketing_addit_prices']);
            if ($marketing->marketing_delivery_vehicles()->exists()) {
                $data->load(['marketing_delivery_vehicles.marketing_product.product', 'marketing_delivery_vehicles.uom', 'marketing_delivery_vehicles.sender', 'marketing_delivery_vehicles.supplier']);
            }

            $param = [
                'title'          => 'Penjualan > Realisasi',
                'data'           => $data,
                'is_realization' => true,
            ];

            if (Constants::MARKETING_STATUS[$marketing->marketing_status] !== 'Final' && Constants::MARKETING_STATUS[$marketing->marketing_status] !== 'Realisasi') {
                throw new \Exception('Status Penjualan belum final');
            }

            if ($req->isMethod('post')) {

                $input = $req->all();

                if (! $req->has('marketing_products')) {
                    return redirect()->back()
                        ->with('error', 'Produk Penjualan tidak boleh kosong')
                        ->withInput($input);
                }

                $success = [];

                DB::transaction(function() use ($req, $marketing, &$success) {
                    $input            = $req->all();
                    $productPrice     = 0;
                    $additPrice       = 0;
                    $existingDoc      = $marketing->doc_reference ?? null;
                    $docReferencePath = '';
                    $deliveryFees     = 0;

                    if (isset($input['doc_reference'])) {
                        if ($existingDoc) {
                            FileHelper::delete($existingDoc);
                        }
                        $docUrl = FileHelper::upload($input['doc_reference'], constants::MARKETING_DOC_REFERENCE_PATH);
                        if (! $docUrl['status']) {
                            throw new \Exception($docUrl['message']);
                        }
                        $docReferencePath = $docUrl['url'];
                    } else {
                        $docReferencePath = $existingDoc;
                    }

                    $marketing->update([
                        'sold_at'       => date('Y-m-d', strtotime($input['sold_at'])),
                        'doc_reference' => $docReferencePath,
                        'realized_at'   => $input['realized_at'] ? date('Y-m-d', strtotime($input['realized_at'])) : null,
                        'notes'         => $input['notes'],
                        'sales_id'      => $input['sales_id'] ?? null,
                        'tax'           => $input['tax'],
                        'discount'      => Parser::parseLocale($input['discount']),
                    ]);

                    if ($input['realized_at']) {
                        $marketing->update([
                            'marketing_status' => array_search(
                                'Realisasi',
                                Constants::MARKETING_STATUS
                            ),
                        ]);
                    }

                    $marketing->marketing_delivery_vehicles()->delete();
                    if ($req->has('marketing_delivery_vehicles')) {
                        $arrVehicle = $req->input('marketing_delivery_vehicles');

                        foreach ($arrVehicle as $key => $value) {
                            $qty         = Parser::parseLocale($value['qty']);
                            $deliveryFee = Parser::parseLocale($value['delivery_fee']);

                            $deliveryFees += $deliveryFee;

                            $arrVehicle[$key] = [
                                'marketing_id'         => $marketing->marketing_id,
                                'plat_number'          => $value['plat_number'],
                                'supplier_id'          => $value['supplier_id'],
                                'marketing_product_id' => $value['marketing_product_id'],
                                'qty'                  => $qty,
                                'uom_id'               => $value['uom_id'],
                                'delivery_fee'         => $deliveryFee,
                                'exit_at'              => date('Y-m-d H:i', strtotime($value['exit_at'])),
                                'sender_id'            => $value['sender_id'],
                                'driver_name'          => $value['driver_name'],
                            ];
                        }
                        MarketingDeliveryVehicle::insert($arrVehicle);
                    }

                    if ($req->has('marketing_products')) {
                        $arrProduct = $req->input('marketing_products');

                        foreach ($arrProduct as $key => $value) {
                            $price       = Parser::parseLocale($value['price']);
                            $weightTotal = Parser::parseLocale($value['weight_total']);
                            $qty         = Parser::parseLocale($value['qty']);

                            $weightAvg  = $weightTotal / max($qty, 1);
                            $totalPrice = $price * $qty;
                            $productPrice += $totalPrice;

                            $arrProduct[$key]['price']        = $price;
                            $arrProduct[$key]['weight_avg']   = $weightAvg;
                            $arrProduct[$key]['weight_total'] = $weightTotal;
                            $arrProduct[$key]['qty']          = $qty;
                            MarketingProduct::find($value['marketing_product_id'])->update($arrProduct);
                        }
                    }

                    $marketing->marketing_addit_prices()->delete();
                    if ($req->has('marketing_addit_prices')) {
                        $arrPrice = $req->input('marketing_addit_prices');
                        $create   = false;
                        foreach ($arrPrice as $key => $value) {
                            $item  = $value['item'] ?? null;
                            $price = Parser::parseLocale($value['price'] ?? null);
                            $additPrice += $price;

                            if ($item && $price) {
                                $create                         = true;
                                $arrPrice[$key]['marketing_id'] = $marketing->marketing_id;
                                $arrPrice[$key]['item']         = $item;
                                $arrPrice[$key]['price']        = $price;
                            }
                        }
                        if ($create) {
                            MarketingAdditPrice::insert($arrPrice);
                        }
                    }

                    if (isset($marketing->realized_at)) {
                        $marketing->marketing_products->each(function($product) {
                            $input = [];

                            $input['product_id']   = $product->product_id;
                            $input['warehouse_id'] = $product->warehouse_id;
                            $input['stocked_by']   = 'Penjualan';
                            $input['stock_date']   = date('Y-m-d');
                            $input['increase']     = 0;
                            $input['decrease']     = $product->qty;

                            $triggerStock = StockLog::triggerStock($input);

                            if (! $triggerStock['result']) {
                                throw new \Exception($triggerStock['message']);
                            }
                        });

                        $success['success'] = 'Data Berhasil direalisasikan';
                    } else {
                        $success['success'] = 'Data Berhasil disimpan sebagai draft';
                    }

                    $subTotal         = $productPrice;
                    $subTotalAfterTax = $productPrice + ($productPrice * (($input['tax'] ?? 0) / 100)) - Parser::parseLocale($input['discount']);

                    $marketing->update([
                        'sub_total'   => $subTotal,
                        'grand_total' => $subTotalAfterTax + $additPrice + $deliveryFees,
                    ]);
                });

                return redirect()->route('marketing.list.index')->with($success);
            }

            return view('marketing.list.realization', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Search the specified resource from storage.
     */
    public function searchMarketing(Request $req)
    {
        $search = $req->input('q');
        $query  = Marketing::with(['customer', 'company'])
            ->where('id_marketing', 'like', "%{$search}%");
        $queryParams = $req->query();
        $queryParams = Arr::except($queryParams, ['q']);
        foreach ($queryParams as $key => $value) {
            $query->where($key, $value);
        }

        $data = $query->get();

        return response()->json($data->map(function($marketing) {
            return [
                'id'   => $marketing->marketing_id,
                'text' => $marketing->id_marketing,
                'data' => $marketing,
            ];
        }));
    }

    /**
     * Search the specified resource from storage.
     */
    public function approve(Request $req, Marketing $marketing)
    {
        DB::beginTransaction();
        try {
            $input = $req->all();

            $success          = ['success' => 'Penjualan berhasil ditolak'];
            $approved_at      = null;
            $marketing_status = array_search('Ditolak', Constants::MARKETING_STATUS);

            if ($input['is_approved'] == 1) {
                $success          = ['success' => 'Penjualan berhasil disetujui'];
                $approved_at      = date('Y-m-d H:i:s');
                $marketing_status = $input['marketing_status'];
            }

            $marketing->update([
                'is_approved'      => $input['is_approved'],
                'approver_id'      => Auth::id(),
                'approval_notes'   => $input['approval_notes'],
                'approved_at'      => $approved_at,
                'marketing_status' => $marketing_status,
            ]);

            DB::commit();

            return redirect()->route('marketing.list.index')->with($success);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchProductByWarehouse(Request $req)
    {
        $warehouseId = $req->id;
        $selected    = [];

        if ($req->has('selected')) {
            $selected = json_decode($req->query('selected'), true);

            $excludedProductIds = collect($selected)
                ->filter(fn ($item) => $item[0] == $warehouseId)
                ->pluck(1)
                ->unique()
                ->toArray();
        }

        $query = ProductWarehouse::where('warehouse_id', $warehouseId)
            ->with(['product', 'product.uom'])
            ->whereHas('product', fn ($p) => $p->where('can_be_sold', 1));

        if (! empty($excludedProductIds)) {
            $query->whereNotIn('product_id', $excludedProductIds);
        }

        $productWarehouses = $query->get();

        $val = $productWarehouses->map(function($productWarehouse) {
            return [
                'id'       => $productWarehouse->product_id,
                'text'     => $productWarehouse->product->name,
                'qty'      => $productWarehouse->quantity,
                'price'    => ($productWarehouse->product->selling_price ?? $productWarehouse->product->product_price) ?? 0,
                'uom_id'   => $productWarehouse->product->uom_id,
                'uom_name' => $productWarehouse->product->uom->name,
                'data'     => $productWarehouse,
            ];
        });

        return response()->json($val);
    }
}
