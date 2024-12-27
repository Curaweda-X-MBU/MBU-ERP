<?php

namespace App\Http\Controllers\Marketing;

use App\Constants;
use App\Helpers\FileHelper;
use App\Helpers\Parser;
use App\Http\Controllers\Controller;
use App\Models\Inventory\StockLog;
use App\Models\Marketing\Marketing;
use App\Models\Marketing\MarketingAdditPrice;
use App\Models\Marketing\MarketingProduct;
use App\Models\Marketing\MarketingReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data  = MarketingReturn::with('marketing')->get();
            $param = [
                'title' => 'Penjualan > Retur',
                'data'  => $data,
            ];

            return view('marketing.return.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function add(Request $req, Marketing $marketing)
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
            ]);
            $param = [
                'title'     => 'Penjualan > Retur > Tambah',
                'is_return' => true,
                'data'      => $data,
            ];

            if (Constants::MARKETING_STATUS[$marketing->marketing_status] !== 'Realisasi') {
                throw new \Exception('Status Penjualan belum realisasi');
            }

            if ($req->isMethod('post')) {
                DB::transaction(function() use ($req, $marketing) {
                    $input            = $req->all();
                    $productPrice     = 0;
                    $additPrice       = 0;
                    $existingDoc      = $marketing->doc_reference ?? null;
                    $docReferencePath = '';

                    if (isset($input['doc_reference'])) {
                        if ($existingDoc) {
                            FileHelper::delete($existingDoc);
                        }
                        $docUrl = FileHelper::upload($input['doc_reference'], Constants::MARKETING_DOC_REFERENCE_PATH);
                        if (! $docUrl['status']) {
                            throw new \Exception($docUrl['message']);
                        }
                        $docReferencePath = $docUrl['url'];
                    } else {
                        $docReferencePath = $existingDoc;
                    }

                    $createdReturn = MarketingReturn::create([
                        'marketing_id'          => $marketing->marketing_id,
                        'return_status'         => array_search('Diajukan', Constants::MARKETING_RETURN_STATUS),
                        'payment_return_status' => array_search('Tempo', Constants::MARKETING_PAYMENT_STATUS),
                        'return_at'             => date('Y-m-d', strtotime($input['return_at'])),
                        'total_return'          => 0,
                    ]);

                    $marketing->update([
                        'marketing_return_id' => $createdReturn->marketing_return_id,
                        'doc_reference'       => $docReferencePath,
                        'notes'               => $input['notes'],
                        'tax'                 => $input['tax'],
                        'discount'            => Parser::parseLocale($input['discount']),
                    ]);

                    if ($req->has('marketing_products')) {
                        $arrProduct = $req->input('marketing_products');

                        foreach ($arrProduct as $key => $value) {
                            $price     = Parser::parseLocale($value['price']);
                            $weightAvg = Parser::parseLocale($value['weight_avg']);
                            $qty       = Parser::parseLocale($value['qty']);

                            $weightTotal = $weightAvg * $qty;
                            $totalPrice  = $price     * $weightTotal;
                            $productPrice += $totalPrice;

                            $returnQty = ['return_qty' => $qty];
                            MarketingProduct::find($value['marketing_product_id'])->update($returnQty);
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

                    $subTotal = $productPrice + ($productPrice * (($input['tax'] ?? 0) / 100)) - Parser::parseLocale($input['discount']);

                    $grandTotal = $subTotal + $additPrice;

                    $createdReturn->update([
                        'total_return'   => $grandTotal,
                        'invoice_number' => 'CN'.$createdReturn->marketing_return_id,
                    ]);
                });

                $success = ['success' => 'Retur Berhasil Diajukan'];

                return redirect()->route('marketing.return.detail', $marketing->marketing_id)->with($success);
            }

            return view('marketing.return.add', $param);
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
                'marketing_return',
            ]);
            $param = [
                'title' => 'Penjualan > Retur > Detail',
                'data'  => $data,
            ];

            return view('marketing.return.detail', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $req, Marketing $marketing)
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
                'marketing_return',
            ]);
            $param = [
                'title'     => 'Penjualan > Retur > Edit',
                'is_return' => true,
                'is_edit'   => true,
                'data'      => $data,
            ];

            if ($req->isMethod('post')) {
                DB::transaction(function() use ($req, $marketing) {
                    $input            = $req->all();
                    $productPrice     = 0;
                    $additPrice       = 0;
                    $existingDoc      = $marketing->doc_reference ?? null;
                    $docReferencePath = '';

                    if (isset($input['doc_reference'])) {
                        if ($existingDoc) {
                            FileHelper::delete($existingDoc);
                        }
                        $docUrl = FileHelper::upload($input['doc_reference'], Constants::MARKETING_DOC_REFERENCE_PATH);
                        if (! $docUrl['status']) {
                            throw new \Exception($docUrl['message']);
                        }
                        $docReferencePath = $docUrl['url'];
                    } else {
                        $docReferencePath = $existingDoc;
                    }

                    $marketing->update([
                        'doc_reference' => $docReferencePath,
                        'notes'         => $input['notes'],
                        'tax'           => $input['tax'],
                        'discount'      => Parser::parseLocale($input['discount']),
                    ]);

                    if ($req->has('marketing_products')) {
                        $arrProduct = $req->input('marketing_products');

                        foreach ($arrProduct as $key => $value) {
                            $price     = Parser::parseLocale($value['price']);
                            $weightAvg = Parser::parseLocale($value['weight_avg']);
                            $qty       = Parser::parseLocale($value['qty']);

                            $weightTotal = $weightAvg * $qty;
                            $totalPrice  = $price     * $weightTotal;
                            $productPrice += $totalPrice;

                            $returnQty = ['return_qty' => $qty];
                            MarketingProduct::find($value['marketing_product_id'])->update($returnQty);
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

                    $subTotal = isset($input['tax'])
                        ? $productPrice + ($productPrice * ($input['tax'] / 100)) - Parser::parseLocale($input['discount'])
                        : $productPrice                                           - Parser::parseLocale($input['discount']);

                    $grandTotal = $subTotal + $additPrice;

                    MarketingReturn::find($marketing->marketing_return_id)->update([
                        'return_at'    => date('Y-m-d', strtotime($input['return_at'])),
                        'total_return' => $grandTotal,
                    ]);
                });

                $success = ['success' => 'Data Berhasil Diubah'];

                return redirect()->route('marketing.return.detail', $marketing->marketing_id)->with($success);
            }

            return view('marketing.return.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(MarketingReturn $return)
    {
        try {
            $return->delete();
            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('marketing.return.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function approve(Request $req, MarketingReturn $return)
    {
        try {
            $input   = $req->all();
            $success = [];

            if ($input['is_approved'] == 0) {
                $return->update([
                    'is_approved'    => 0,
                    'approver_id'    => Auth::id(),
                    'approval_notes' => $input['approval_notes'],
                ]);

                $success = ['success' => 'Retur berhasil ditolak'];
            } else {
                $return->update([
                    'is_approved'    => 1,
                    'approver_id'    => Auth::id(),
                    'return_status'  => array_search('Disetujui', Constants::MARKETING_RETURN_STATUS),
                    'approved_at'    => date('Y-m-d H:i:s'),
                    'approval_notes' => $input['approval_notes'],
                ]);

                $success = ['success' => 'Retur berhasil disetujui'];

                $return->marketing->marketing_products->each(function($product) {
                    $input = [];

                    $input['product_id']   = $product->product_id;
                    $input['warehouse_id'] = $product->warehouse_id;
                    $input['stocked_by']   = 'Retur Penjualan';
                    $input['stock_date']   = date('Y-m-d');
                    $input['increase']     = $product->return_qty ?? 0;
                    $input['decrease']     = 0;

                    $triggerStock = StockLog::triggerStock($input);

                    if (! $triggerStock['result']) {
                        throw new \Exception($triggerStock['message']);
                    }
                });
            }

            return redirect()->route('marketing.return.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
