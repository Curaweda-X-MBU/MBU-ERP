<?php

namespace App\Http\Controllers\Marketing;

use App\Constants;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Customer;
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
        'doc_reference' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120',
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
                $validator = Validator::make($req->all(), self::VALIDATION_RULES_ADD, self::VALIDATION_MESSAGES_ADD);
                $input     = $req->all();
                if ($validator->fails()) {
                    if (isset($input['customer_id'])) {
                        $input['customer_name'] = Customer::find(
                            $req->input('customer_id')
                        )->name;
                    }
                    if (isset($input['company_id'])) {
                        $input['company_name'] = Company::find(
                            $req->input('company_id')
                        )->name;
                    }
                    if (isset($input['sales_id'])) {
                        $input['sales_name'] = User::find(
                            $req->input('sales_id')
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
                DB::transaction(function() use ($req) {
                    $input = $req->all();

                    $company = Auth::user()->department->company;

                    $createdMarketing = Marketing::create([
                        'company_id'     => $company->company_id,
                        'customer_id'    => $input['customer_id'],
                        'sold_at'        => $input['sold_at'],
                        'doc_reference'  => $input['doc_reference'],
                        'notes'          => $input['notes'],
                        'tax'            => $input['tax'],
                        'discount'       => $input['discount'],
                        'sub_total'      => $input['sub_total'],
                        'grand_total'    => $input['grand_total'],
                        'payment_status' => array_search(
                            'Belum Dibayar',
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
                            $price     = str_replace(',', '', $value['price'] ?? 0);
                            $weightAvg = str_replace(',', '', $value['weight_avg'] ?? 0);
                            $qty       = str_replace(',', '', $value['qty'] ?? 0);

                            $weightTotal = $weightAvg * $qty;
                            $totalPrice  = $price     * $qty;

                            $arrProduct[$key] = [
                                'marketing_id' => $createdMarketing->marketing_id,
                                'kandang_id'   => $input['kandang_id'],
                                'product_id'   => $input['product_id'],
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

                    if ($req->has('marketing_addit_prices')) {
                        $arrPrice = $req->input('marketing_addit_prices');

                        foreach ($arrPrice as $key => $value) {
                            $item  = $value['item'];
                            $price = str_replace(',', '', $value['price']);

                            $arrPrice[$key] = [
                                'marketing_id' => $createdMarketing->marketing_id,
                                'item'         => $item,
                                'price'        => $price,
                            ];
                        }

                        MarketingAdditPrice::insert($arrPrice);
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
                $validator = Validator::make($req->all(), self::VALIDATION_RULES_ADD, self::VALIDATION_MESSAGES_ADD);
                $input     = $req->all();

                if ($validator->fails()) {
                    if (isset($input['customer_id'])) {
                        $input['customer_name'] = Customer::find($req->input('customer_id'))->name;
                    }
                    if (isset($input['company_id'])) {
                        $input['company_name'] = Company::find($req->input('company_id'))->name;
                    }
                    if (isset($input['sales_id'])) {
                        $input['sales_name'] = User::find($req->input('sales_id'))->name;
                    }

                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                if (! $req->has('marketing_products')) {
                    return redirect()->back()->with('error', 'Produk Penjualan tidak boleh kosong')->withInput($input);
                }

                DB::transaction(function() use ($req, $marketing) {
                    $input = $req->all();

                    $marketing->update([
                        'customer_id'   => $input['customer_id'],
                        'sold_at'       => $input['sold_at'],
                        'doc_reference' => $input['doc_reference'],
                        'notes'         => $input['notes'],
                        'tax'           => $input['tax'],
                        'discount'      => $input['discount'],
                        'sub_total'     => $input['sub_total'],
                        'grand_total'   => $input['grand_total'],
                    ]);

                    if ($req->has('marketing_products')) {
                        $arrProduct = $req->input('marketing_products');

                        $marketing->marketing_products()->delete();

                        foreach ($arrProduct as $key => $value) {
                            $price     = str_replace(',', '', $value['price'] ?? 0);
                            $weightAvg = str_replace(',', '', $value['weight_avg'] ?? 0);
                            $qty       = str_replace(',', '', $value['qty'] ?? 0);

                            $weightTotal = $weightAvg * $qty;
                            $totalPrice  = $price     * $qty;

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

                    if ($req->has('marketing_addit_prices')) {
                        $arrPrice = $req->input('marketing_addit_prices');

                        $marketing->marketing_addit_prices()->delete();

                        foreach ($arrPrice as $key => $value) {
                            $item  = $value['item'];
                            $price = str_replace(',', '', $value['price']);

                            $arrPrice[$key] = [
                                'marketing_id' => $marketing->marketing_id,
                                'item'         => $item,
                                'price'        => $price,
                            ];
                        }

                        MarketingAdditPrice::insert($arrPrice);
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
                        $input['uom_name'] = Uom::find($req->input('uom_id'))->name;
                    }
                    if (isset($input['sender_id'])) {
                        $input['sender_name'] = User::find($req->input('sender_id'))->name;
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
                            $qty = str_replace(',', '', $value['qty'] ?? 0);

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
}
