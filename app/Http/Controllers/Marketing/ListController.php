<?php

namespace App\Http\Controllers\Marketing;

use App\Constants;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Customer;
use App\Models\DataMaster\Product;
use App\Models\DataMaster\Uom;
use App\Models\Marketing\Marketing;
use App\Models\Marketing\MarketingAdditPrice;
use App\Models\Marketing\MarketingDeliveryVehicle;
use App\Models\Marketing\MarketingProduct;
use App\Models\UserManagement\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ListController extends Controller
{
    private const VALIDATION_RULES_ADD = [
        'customer_id'   => 'required',
        'sold_at'       => 'required',
        'doc_reference' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
        'sales_id'      => 'required',
        'sub_total'     => 'required',
        'grand_total'   => 'required',

        // Validasi marketing_products
        'marketing_products.*.kandang_id' => 'required',
        'marketing_products.*.product_id' => 'required',
        'marketing_products.*.price'      => 'required',
        'marketing_products.*.weight_avg' => 'required',
        'marketing_products.*.uom_id'     => 'required',
        'marketing_products.*.qty'        => 'required',
    ];

    private const VALIDATION_RULES_REALIZATION = [
        'plat_number' => 'required',
        'qty'         => 'required',
        'uom_id'      => 'required',
        'exit_at'     => 'required',
        'sender_id'   => 'required',
        'driver_name' => 'required',
    ];

    private const VALIDATION_MESSAGES_ADD = [
        'customer_id.required'   => 'Nama Pelanggan tidak boleh kosong',
        'sold_at.required'       => 'Tanggal Penjualan tidak boleh kosong',
        'sold_at.date'           => 'Format tanggal tidak valid',
        'doc_reference.required' => 'Referensi Dokumen tidak boleh kosong',
        'doc_reference.file'     => 'Referensi Dokumen tidak valid',
        'doc_reference.mimes'    => 'Referensi hanya boleh pdf, jpeg, png, atau jpg',
        'doc_reference.max'      => 'Ukuran file tidak boleh lebih dari 5MB',
        'sales_id.required'      => 'Nama Sales tidak boleh kosong',
        'sub_total.required'     => 'Total setelah pajak & diskon tidak boleh kosong',
        'grand_total.required'   => 'Total Piutang tidak boleh kosong',

        // Validasi marketing_products
        'marketing_products.*.kandang_id.required' => 'Nama Kandang tidak boleh kosong',
        'marketing_products.*.product_id.required' => 'Nama Produk tidak boleh kosong',
        'marketing_products.*.price.required'      => 'Harga Satuan tidak boleh kosong',
        'marketing_products.*.weight_avg.required' => 'Weight Avg tidak boleh kosong',
        'marketing_products.*.uom_id.required'     => 'Uom tidak boleh kosong',
        'marketing_products.*.qty.required'        => 'Qty tidak boleh kosong',
    ];

    private const VALIDATION_MESSAGES_REALIZATION = [
        'plat_number.required' => 'No Polisi tidak boleh kosong',
        'qty.required'         => 'Jumlah tidak boleh kosong',
        'uom_id.required'      => 'Uom tidak boleh kosong',
        'exit_at.required'     => 'Waktu Keluar Kandang tidak boleh kosong',
        'sender_id.required'   => 'Nama Pengirim tidak boleh kosong',
        'driver_name.required' => 'Nama Driver tidak boleh kosong',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data  = Marketing::with(['customer', 'company'])->get();
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
                DB::transaction(function() use ($req) {
                    $input = $req->all();

                    $totalPrices = 0;

                    $company = Auth::user()->department->company;

                    $docReferencePath = '';
                    if ($req->hasFile('doc_reference')) {
                        $docUrl = FileHelper::upload($input['doc_reference'], Constants::MARKETING_DOC_REFERENCE_PATH);
                        if (! $docUrl['status']) {
                            return redirect()->back()->with('error', $docUrl['message'].' '.$input['doc_reference'])->withInput();
                        }
                        $docReferencePath = $docUrl['url'];
                    }

                    $createdMarketing = Marketing::create([
                        'company_id'     => $company->company_id,
                        'customer_id'    => $input['customer_id'],
                        'sold_at'        => date('Y-m-d', strtotime($input['sold_at'])),
                        'doc_reference'  => $docReferencePath,
                        'notes'          => $input['notes'],
                        'sales_id'       => $input['sales_id'],
                        'tax'            => $input['tax'],
                        'discount'       => parseLocale($input['discount']),
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

                    if ($req->has('marketing_products')) {
                        $arrProduct = $req->input('marketing_products');

                        foreach ($arrProduct as $key => $value) {
                            $price     = parseLocale($value['price']);
                            $weightAvg = parseLocale($value['weight_avg']);
                            $qty       = parseLocale($value['qty']);

                            $weightTotal = $weightAvg * $qty;
                            $totalPrice  = $price     * $weightTotal;

                            $totalPrices += $totalPrice;

                            $arrProduct[$key]['marketing_id'] = $createdMarketing->marketing_id;
                            $arrProduct[$key]['kandang_id']   = $value['kandang_id'];
                            $arrProduct[$key]['product_id']   = $value['product_id'];
                            $arrProduct[$key]['price']        = $price;
                            $arrProduct[$key]['weight_avg']   = $weightAvg;
                            $arrProduct[$key]['uom_id']       = $value['uom_id'];
                            $arrProduct[$key]['qty']          = $qty;
                            $arrProduct[$key]['weight_total'] = $weightTotal;
                            $arrProduct[$key]['total_price']  = $totalPrice;
                        }

                        MarketingProduct::insert($arrProduct);
                    }

                    if ($req->has('marketing_products')) {
                        $createdMarketing->update([
                            'sub_total'   => $totalPrices,
                            'grand_total' => isset($input['tax'])
                                ? $totalPrices + ($totalPrices * ($input['tax'] / 100)) - parseLocale($input['discount'])
                                : $totalPrices                                          - parseLocale($input['discount']),
                        ]);
                    }

                    if ($req->has('marketing_addit_prices')) {
                        $arrPrice = $req->input('marketing_addit_prices');
                        $item     = $value['item'] ?? null;
                        $price    = parseLocale($value['price']);

                        if ($item && $price) {
                            foreach ($arrPrice as $key => $value) {
                                $arrPrice[$key]['marketing_id'] = $createdMarketing->marketing_id;
                                $arrPrice[$key]['item']         = $item;
                                $arrPrice[$key]['price']        = $price;
                            }
                            MarketingAdditPrice::insert($arrPrice);
                        }
                    }

                    $createdMarketing->update([
                        'id_marketing' => "DO.{$company->alias}.{$createdMarketing->marketing_id}",
                    ]);
                });

                $success = ['success' => 'Data Berhasil disimpan'];

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
            $data  = $marketing->with(['customer', 'company'])->get();
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
            $data  = $marketing->with(['company', 'customer', 'sales', 'marketing_products', 'marketing_addit_prices'])->get();
            $param = [
                'title' => 'Penjualan > Edit',
                'data'  => $data,
            ];

            if ($req->isMethod('post')) {
                $input     = $req->all();
                $validator = Validator::make($input, self::VALIDATION_RULES_ADD, self::VALIDATION_MESSAGES_ADD);
                if ($validator->fails()) {
                    if (isset($input['customer_id'])) {
                        $input['customer_name'] = Customer::find(
                            $input['customer_id']
                        )->name;
                    }
                    if (isset($input['company_id'])) {
                        $input['company_name'] = Company::find(
                            $input['company_id']
                        )->name;
                    }
                    if (isset($input['sales_id'])) {
                        $input['sales_name'] = User::find(
                            $input['sales_id']
                        )->name;
                    }

                    return redirect()
                        ->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                if (! $req->has('marketing_products')) {
                    return redirect()->back()->with('error', 'Produk Penjualan tidak boleh kosong')->withInput($input);
                }

                DB::transaction(function() use ($req, $marketing) {
                    $input = $req->all();

                    $totalPrices = 0;

                    $existingDoc      = $marketing->doc_reference ?? null;
                    $docReferencePath = '';
                    if ($req->hasFile('doc_reference')) {
                        if ($existingDoc) {
                            FileHelper::delete($existingDoc);
                        }

                        $docUrl = FileHelper::upload($input['doc_reference'], constants::MARKETING_DOC_REFERENCE_PATH);
                        if (! $docUrl['status']) {
                            return redirect()->back()->with('error', $docUrl['message'].' '.$input['doc_reference'])->withInput();
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
                        'sales_id'      => $input['sales_id'],
                        'tax'           => $input['tax'],
                        'discount'      => $input['discount'],
                    ]);

                    if ($req->has('marketing_products')) {
                        $arrProduct = $req->input('marketing_products');

                        $marketing->marketing_products()->delete();

                        foreach ($arrProduct as $key => $value) {
                            $price     = parseLocale($value['price']);
                            $weightAvg = parseLocale($value['weight_avg']);
                            $qty       = parseLocale($value['qty']);

                            $weightTotal = $weightAvg * $qty;
                            $totalPrice  = $price     * $qty;
                            $totalPrices += $totalPrice;

                            $arrProduct[$key] = [
                                'marketing_id' => $marketing->marketing_id,
                                'kandang_id'   => $value['kandang_id'],
                                'product_id'   => $value['product_id'],
                                'price'        => $price,
                                'weight_avg'   => $weightAvg,
                                'uom_id'       => $input['uom_id'],
                                'qty'          => $qty,
                                'weight_total' => $weightTotal,
                                'total_price'  => $totalPrice,
                            ];
                        }

                        MarketingProduct::insert($arrProduct);
                    }

                    $marketing->update([
                        'sub_total'   => $totalPrices,
                        'grand_total' => isset($input['tax'])
                            ? $totalPrices + ($totalPrices * ($input['tax'] / 100)) - parseLocale($input['discount'])
                            : $totalPrices                                          - parseLocale($input['discount']),
                    ]);

                    if ($req->has('marketing_addit_prices')) {
                        $marketing->marketing_addit_prices()->delete();

                        $arrPrice = $req->input('marketing_addit_prices');
                        $item     = $value['item'] ?? null;
                        $price    = parseLocale($value['price']);

                        if ($item && $price) {
                            foreach ($arrPrice as $key => $value) {
                                $arrPrice[$key]['marketing_id'] = $marketing->marketing_id;
                                $arrPrice[$key]['item']         = $item;
                                $arrPrice[$key]['price']        = $price;
                            }
                            MarketingAdditPrice::insert($arrPrice);
                        }
                    }
                });

                $success = ['success' => 'Data Berhasil diubah'];

                return redirect()->route('marketing.list.index')->with($success);
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
    public function delete(Marketing $id)
    {
        try {
            $id->delete();
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
            $data  = $marketing->with(['company', 'customer', 'sales', 'marketing_products', 'marketing_addit_prices'])->get();
            $param = [
                'title' => 'Penjualan > Realisasi',
                'data'  => $data,
            ];

            if ($req->isMethod('post')) {
                $validator = Validator::make($req->all(), self::VALIDATION_RULES_REALIZATION, self::VALIDATION_MESSAGES_REALIZATION);
                $input     = $req->all();

                if ($validator->fails()) {
                    if (isset($input['uom_id'])) {
                        $input['uom_name'] = Uom::find($input['uom_id'])->name;
                    }
                    if (isset($input['sender_id'])) {
                        $input['sender_name'] = User::find($input['sender_id'])->name;
                    }

                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                if (! $req->has('marketing_products')) {
                    return redirect()->back()->with('error', 'Produk Penjualan tidak boleh kosong')->withInput($input);
                }

                DB::transaction(function() use ($req, $marketing) {
                    if ($req->has('marketing_delivery_vehicles')) {
                        $arrVehicle = $req->input('marketing_delivery_vehicles');

                        foreach ($arrVehicle as $key => $value) {
                            $qty = parseLocale($value['qty']);

                            $arrVehicle[$key] = [
                                'marketing_id' => $marketing->marketing_id,
                                'plat_number'  => $value['plat_number'],
                                'qty'          => $qty,
                                'exit_at'      => date('Y-m-d H:i', strtotime($value['exit_at'])),
                                'driver_name'  => $value['driver_name'],
                            ];
                        }

                        MarketingDeliveryVehicle::insert($arrVehicle);
                    }
                });
                $success = ! empty($marketing->realized_at)
                    ? ['success' => 'Data Berhasil direalisasikan']
                    : ['success' => 'Data Berhasil disimpan sebagai draft'];

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
        $search      = $req->input('q');
        $query       = Marketing::with(['customer', 'company'])->where('id_marketing', 'like', "%{$search}%");
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
        try {
            $input         = $req->all();
            $marketingData = $marketing->get();

            $success = [];

            if ($input['is_approved'] === 0) {
                $marketingData->update([
                    'is_approved'    => array_search('Tidak Disetujui', Constants::MARKETING_APPROVAL),
                    'approver_id'    => Auth::id(),
                    'approval_notes' => $input['approval_notes'],
                ]);

                $success = ['success' => 'Data berhasil ditolak'];
            } else {
                $marketingData->update([
                    'is_approved' => array_search('Disetujui', Constants::MARKETING_APPROVAL),
                    'approver_id' => Auth::id(),
                    'approved_at' => date('Y-m-d H:i:s'),
                ]);

                $success = ['success' => 'Data berhasil disetujui'];
            }

            return redirect()->route('marketing.list.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchProductByKandang(Request $req)
    {
        $kandangId = $req->id;

        $products = Product::whereHas('product_warehouse.warehouse.kandang', function($q) use ($kandangId) {
            $q->where('kandang_id', $kandangId);
        })->with(['product_warehouse' => function($q) {
            $q->select('product_id', 'quantity');
        }])->get();

        $val = $products->map(function($product) {
            return [
                'id'   => $product->product_id,
                'text' => $product->name,
                'qty'  => $product->product_warehouse->sum('quantity'),
                'data' => $product,
            ];
        });

        return response()->json($val);
    }
}
