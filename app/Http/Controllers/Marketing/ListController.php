<?php

namespace App\Http\Controllers\Marketing;

use App\Constants;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Customer;
use App\Models\Marketing\Marketing;
use App\Models\Marketing\MarketingAdditPrice;
use App\Models\Marketing\MarketingProduct;
use App\Models\UserManagement\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

class ListController extends Controller
{
    private const VALIDATION_RULES = [
        'company_id'         => 'required',
        'location_id'        => 'required',
        'kandang_id'         => 'required',
        'customer_id'        => 'required',
        'sold_at'            => 'required',
        'realized_at'        => 'required',
        'doc_reference'      => 'required',
        'marketing_products' => 'required',
        'notes'              => 'required',
        'sales_id'           => 'required',
        'tax'                => 'required',
        'discount'           => 'required',
    ];

    private const VALIDATION_MESSAGES = [];

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
            return redirect()->back()->with('error', $e->getMessage())->withInput();
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
                $validator = Validator::make($req->all(), self::VALIDATION_RULES, self::VALIDATION_MESSAGES);
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
                DB::transaction(function() use ($req) {
                    $input = $req->all();

                    $company = Auth::user()->department->company;

                    $createdMarketing = Marketing::create([
                        'company_id'       => $company->company_id,
                        'customer_id'      => $input['customer_id'],
                        'sold_at'          => $input['sold_at'],
                        'doc_reference'    => $input['doc_reference'],
                        'notes'            => $input['notes'],
                        'tax'              => $input['tax'],
                        'discount'         => $input['discount'],
                        'sub_total'        => $input['sub_total'],
                        'grand_total'      => $input['grand_total'],
                        'payment_status'   => array_search('Belum Dibayar', Constants::MARKETING_PAYMENT_STATUS),
                        'marketing_status' => array_search('Diajukan', Constants::MARKETING_STATUS),
                        'created_by'       => Auth::id(),
                    ]);

                    if ($req->has('marketing_products')) {
                        $arrProduct = $req->input('marketing_products');
                        foreach ($arrProduct as $key => $value) {
                            $arrProduct[$key]['marketing_id'] = $createdMarketing->marketing_id;
                            $arrProduct[$key]['kandang_id']   = $input['kandang_id'];
                            $arrProduct[$key]['product_id']   = $input['product_id'];
                            $arrProduct[$key]['price']        = str_replace(',', '', $value['price']);
                            $arrProduct[$key]['weight_avg']   = $input['weight_avg'];
                            $arrProduct[$key]['qty']          = str_replace(',', '', $value['qty']);
                            $arrProduct[$key]['weight_total'] = $input['weight_avg'] * $input['qty'];
                            $arrProduct[$key]['total_price']  = $input['price']      * $input['qty'];
                        }
                        MarketingProduct::insert($arrProduct);
                    }

                    if ($req->has('marketing_addit_prices')) {
                        $arrPrice = $req->input('marketing_addit_prices');
                        foreach ($arrPrice as $key => $value) {
                            $arrPrice[$key]['marketing_id'] = $createdMarketing->marketing_id;
                            $arrPrice[$key]['item']         = $input['item'];
                            $arrPrice[$key]['price']        = $input['price'];
                        }
                        MarketingAdditPrice::insert($arrPrice);
                    }

                    $createdMarketing->update([
                        'id_marketing' => "DO.{$company->alias}.{$createdMarketing->marketing_id}",
                    ]);
                });

                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()->route('marketing.list.index')->with($success);
            }

            return view('marketing.list.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function detail(Marketing $marketing)
    {
        try {
            $data  = Marketing::findOrFail($marketing->marketing_id)->with(['customer', 'company'])->get();
            $param = [
                'title' => 'Penjualan > Detail',
                'data'  => $data,
            ];

            return view('marketing.list.detail', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Marketing $marketing)
    {
        try {
            $param = [
                'title' => 'Penjualan > Edit',
            ];

            return view('marketing.list.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
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
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        }
    }

    /**
     * Search the specified resource from storage.
     */
    public function realization(Marketing $marketing)
    {
        try {
            $param = [
                'title' => 'Penjualan > Realisasi',
            ];

            return view('marketing.list.realization', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Search the specified resource from storage.
     */
    public function payment(Marketing $marketing)
    {
        try {
            $param = [
                'title' => 'Penjualan > Pembayaran',
            ];

            return view('marketing.list.payment', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Search the specified resource from storage.
     */
    public function searchMarketing(Marketing $marketing)
    {
        //
    }
}
