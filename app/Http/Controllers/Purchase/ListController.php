<?php

namespace App\Http\Controllers\Purchase;

use App\Constants;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Nonstock;
use App\Models\DataMaster\Warehouse;
use App\Models\Expense\Expense;
use App\Models\Inventory\ProductWarehouse;
use App\Models\Inventory\StockAvailability;
use App\Models\Inventory\StockLog;
use App\Models\Purchase\Purchase;
use App\Models\Purchase\PurchaseItem;
use App\Models\Purchase\PurchaseItemAlocation;
use App\Models\Purchase\PurchaseItemReception;
use App\Models\Purchase\PurchaseOther;
use App\Models\Purchase\PurchasePayment;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListController extends Controller
{
    private const VALIDATION_RULES = [];

    private const VALIDATION_MESSAGES = [];

    public function index(Request $req)
    {
        try {
            $data      = Purchase::with(['supplier', 'createdBy']);
            $request   = $req->all();
            $rows      = $req->has('rows') ? $req->get('rows') : 10;
            $arrAppend = [
                'rows' => $rows,
                'page' => 1,
            ];
            foreach ($request as $key => $value) {
                if (intval($value) >= 0 && ! in_array($key, ['rows', 'page'])) {
                    $data            = $data->where($key, $value);
                    $arrAppend[$key] = $value;
                }
            }
            $data = $data
                ->orderBy('purchase_id', 'DESC')
                ->paginate($rows);
            $data->appends($arrAppend);
            $param = [
                'title'  => 'Pembelian',
                'data'   => $data,
                'status' => Constants::PURCHASE_STATUS,
            ];

            return view('purchase.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req)
    {
        try {
            $param = [
                'title'  => 'Pembelian > Tambah',
                'status' => Constants::PURCHASE_STATUS,
                'type'   => Constants::KANDANG_TYPE,
            ];

            if ($req->isMethod('post')) {
                $input        = $req->input();
                $purchaseItem = $input['purchase_item'] ?? [];
                if (count($purchaseItem) > 0) {
                    DB::transaction(function() use ($req, $input, $purchaseItem) {
                        $alias        = auth()->user()->department->company->alias;
                        $lastData     = Purchase::orderBy('purchase_id', 'desc')->first();
                        $lastNumber   = $lastData ? (int) $lastData->purchase_id : 0;
                        $increment    = $lastNumber + 1;
                        $prNumber     = str_pad($increment, 5, '0', STR_PAD_LEFT);
                        $approvalLine = [
                            'status'    => 0,
                            'notes'     => null,
                            'action_by' => Auth::user()->name ?? null,
                            'date'      => date('Y-m-d H:i:s'),
                        ];

                        $warehouseIds   = collect($purchaseItem)->pluck('warehouse_id')->unique()->values()->toArray();
                        $purchaseInsert = Purchase::create([
                            'pr_number'     => 'PR-'.$alias.'-'.$prNumber,
                            'supplier_id'   => $input['supplier_id'],
                            'warehouse_ids' => $warehouseIds,
                            'require_date'  => date('Y-m-d', strtotime($input['require_date'])),
                            'notes'         => $input['notes'] ?? null,
                            'status'        => $this->getStatus('Approval Manager'),
                            'approval_line' => json_encode([$approvalLine]),
                            'created_by'    => Auth::user()->user_id ?? '',
                        ]);

                        $purchaseId = $purchaseInsert->purchase_id;
                        $grouped    = collect($purchaseItem)->groupBy('product_id')->map(function($items) {
                            return [
                                'product_id' => $items->first()['product_id'],
                                'qty'        => $items->sum(fn ($item) => str_replace('.', '', str_replace(',', '.', $item['qty']))),
                            ];
                        })->values()->toArray();

                        if ($req->has('purchase_item')) {
                            foreach ($grouped as $k => $v) {
                                $purchaseItemInsert = PurchaseItem::create([
                                    'purchase_id' => $purchaseId,
                                    'product_id'  => $v['product_id'],
                                    'qty'         => $v['qty'],
                                ]);

                                foreach ($purchaseItem as $key => $value) {
                                    if ($v['product_id'] === $value['product_id']) {
                                        $alocationQty = str_replace('.', '', str_replace(',', '.', $value['qty']));
                                        PurchaseItemAlocation::create([
                                            'purchase_item_id' => $purchaseItemInsert->purchase_item_id,
                                            'warehouse_id'     => $value['warehouse_id'],
                                            'alocation_qty'    => $alocationQty,
                                        ]);
                                    }
                                }
                            }
                        }
                    });
                } else {
                    return redirect()->back()->with('error', 'Data Item Pembelian tidak boleh kosong');
                }

                $success = ['success' => 'Data Berhasil Diajukan'];

                return redirect()->route('purchase.index')->with($success);
            }

            return view('purchase.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req)
    {
        try {
            $purchase = Purchase::with([
                'supplier', 'createdBy', 'warehouse', 'purchase_item', 'purchase_other', 'purchase_payment',
            ])
                ->with('purchase_item', function($query) {
                    $query->with('product', function($query) {
                        $query->with('product_category');
                        $query->with('uom');
                    });
                })->findOrFail($req->id);
            $param = [
                'title' => 'Pembelian > Edit',
                'data'  => $purchase,
                'type'  => Constants::KANDANG_TYPE,
            ];

            if ($req->isMethod('post')) {
                $input        = $req->input();
                $purchaseItem = $input['purchase_item'] ?? [];
                if (count($purchaseItem) > 0) {
                    DB::transaction(function() use ($req, $input, $purchaseItem, $purchase) {
                        $purchase->update([
                            'supplier_id'  => $input['supplier_id'],
                            'warehouse_id' => $input['warehouse_id'],
                            'require_date' => date('Y-m-d', strtotime($input['require_date'])),
                            'notes'        => $input['notes'] ?? null,
                        ]);

                        $purchaseId = $req->id;
                        if ($req->has('purchase_item')) {
                            PurchaseItem::where('purchase_id', $purchaseId)->delete();
                            foreach ($purchaseItem as $key => $value) {
                                PurchaseItem::create([
                                    'purchase_id' => $purchaseId,
                                    'product_id'  => $value['product_id'],
                                    'qty'         => str_replace('.', '', str_replace(',', '.', $value['qty'])),
                                ]);
                            }
                        }
                    });
                } else {
                    return redirect()->back()->with('error', 'Data Item Pembelian tidak boleh kosong');
                }

                $success = ['success' => 'Data Berhasil Dirubah'];

                return redirect()->route('purchase.detail', $req->id)->with($success);
            }

            return view('purchase.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function copy(Request $req)
    {
        try {
            $purchase = Purchase::with([
                'supplier', 'createdBy', 'purchase_item', 'purchase_other', 'purchase_payment',
            ])
                ->with('purchase_item', function($query) {
                    $query->with(['warehouse', 'project']);
                    $query->with('product', function($query) {
                        $query->with('product_category');
                        $query->with('uom');
                    });
                })->findOrFail($req->id);
            $param = [
                'title' => 'Pembelian > Copy',
                'data'  => $purchase,
            ];

            return view('purchase.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function detail(Request $req)
    {
        try {
            $purchase = Purchase::with([
                'supplier', 'createdBy', 'purchase_other', 'purchase_payment',
            ])
                ->with('purchase_item', function($query) {
                    $query->with(['product', 'purchase_item_reception', 'purchase_item_reception.supplier', 'purchase_item_alocation', 'purchase_item_alocation.warehouse', 'purchase_item_alocation.warehouse.location', 'purchase_item_alocation.warehouse.kandang.user']);
                })
                ->findOrFail($req->id);
            $param = [
                'title'             => 'Pembelian > Detail',
                'data'              => $purchase,
                'purchase_status'   => Constants::PURCHASE_STATUS,
                'purchase_approval' => Constants::PURCHASE_APPROVAL,
                'payment_method'    => Constants::PAYMENT_METHOD,
                'payment_status'    => Constants::PAYMENT_STATUS,
            ];

            if ($req->has('po_number')) {
                $param['title'] = $purchase->po_number;

                return view('purchase.download-po', $param);
            }

            return view('purchase.detail', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function getStatus($strStatus)
    {
        return array_search($strStatus, Constants::PURCHASE_STATUS);
    }

    public function nextPurchaseStatus($role, $data)
    {
        $nextStatus = null;
        switch ($role) {
            case 'Super Admin':
                $nextStatus = $data->status + 1;
                break;
            case 'Manager Area':
                $ovkProduct = collect($data)->filter(function($purchase) {
                    return Str::contains(strtolower($purchase->purchase_item?->product?->product_sub_category?->name ?? ''), 'ovk');
                });
                if (count($ovkProduct) > 0) {
                    $nextStatus = $this->getStatus('Approval Poultry Health');
                } else {
                    $nextStatus = $this->getStatus('Approval Purchasing');
                }
                break;
            case 'Manager Poultry Health':
                $nextStatus = $this->getStatus('Approval Purchasing');
                break;
            case 'Manager Purchasing':
                if ($data->status == $this->getStatus('Produk Diterima')) {
                    $nextStatus = $this->getStatus('Dibayar Sebagian');
                } else {
                    $nextStatus = $this->getStatus('Approval Finance');
                }
                break;
            case 'Manager Finance':
                if ($data->status == $this->getStatus('Dibayar Sebagian')) {
                    if ($data->purchase_payment->amount == $data->grand_total) {
                        $nextStatus = $this->getStatus('Lunas');
                    } else {
                        $nextStatus = $this->getStatus('Dibayar Sebagian');
                    }
                } else {
                    if ($data->grand_total >= 100000000) {
                        $nextStatus = $this->getStatus('Approval Dir. Finance');
                    } else {
                        $nextStatus = $this->getStatus('Produk Diterima');
                    }
                }
                break;
            case 'Direktur Finance':
                $nextStatus = $this->getStatus('Produk Diterima');
                break;
            case 'Staff Finance':
                $nextStatus = $this->getStatus('Dibayar Sebagian');
                break;
            default:
                $nextStatus = $data->status;
                break;
        }

        return $nextStatus;
    }

    public function approve(Request $req)
    {
        try {
            DB::beginTransaction();
            $purchase          = Purchase::with('purchase_item.product.product_sub_category')->findOrFail($req->id);
            $roleName          = Auth::user()->role->name;
            $actionBy          = Auth::user()->name;
            $arrPurchaseStatus = Constants::PURCHASE_STATUS;
            $approvalStatus    = $arrPurchaseStatus[$purchase->status];
            $approvalPurchase  = Constants::PURCHASE_APPROVAL;
            if (($approvalPurchase[$approvalStatus] != $roleName) && $roleName !== 'Super Admin') {
                return redirect()->back()->with('error', 'Kamu tidak memiliki akses untuk menyutujui pembelian ini');
            }
            $nextStatus      = $this->nextPurchaseStatus($roleName, $purchase);
            $currentApprLine = json_decode($purchase->approval_line);
            $status          = $req->has('reject') ? $purchase->status : $nextStatus;
            $notes           = $req->has('reject') ? $req->input('reason') : $req->input('notes');

            $approvalLine = [
                'status'    => $purchase->status,
                'notes'     => $notes,
                'action_by' => $actionBy,
                'date'      => date('Y-m-d H:i:s'),
            ];

            if ($currentApprLine) {
                array_push($currentApprLine, $approvalLine);
            } else {
                $currentApprLine = [$approvalLine];
            }

            $dataApproval = [
                'status'        => $status,
                'rejected'      => $req->has('reject') ? true : false,
                'approval_line' => json_encode($currentApprLine),
            ];

            $successMsg = $req->has('reject') ? 'Data berhasil ditolak' : 'Data berhasil disetujui';

            if ($req->has('save_only')) {
                $dataApproval['status']        = $purchase->status;
                $dataApproval['approval_line'] = $purchase->approval_line;
                $successMsg                    = 'Data berhasil disimpan';
            }

            if ($arrPurchaseStatus[$purchase->status] == 'Approval Purchasing') {
                PurchaseOther::where('purchase_id', $req->id)->delete();
            }

            if ($arrPurchaseStatus[$purchase->status] == 'Produk Diterima') {
                $receivedPurchaseAmount    = 0;
                $notReceivedPurchaseAmount = 0;
                $returPurchaseAmount       = 0;

                $warehouseIds = $purchase->warehouse_ids;
                $arrKandang   = [];
                foreach ($purchase->purchase_item as $key => $value) {
                    for ($i = 0; $i < count($warehouseIds); $i++) {
                        $stockLog     = StockLog::where('purchase_item_id', $value->purchase_item_id)->get();
                        $currentStock = ProductWarehouse::firstOrCreate([
                            'product_id'   => $value->product_id,
                            'warehouse_id' => $warehouseIds[$i],
                        ]);

                        if (count($stockLog) > 0) {
                            foreach ($stockLog as $idx => $val) {
                                if ($currentStock) {
                                    $currentStock->update([
                                        'quantity' => $currentStock->quantity - $val->increase + $val->decrease,
                                    ]);

                                }
                            }
                            StockLog::where('purchase_item_id', $value->purchase_item_id)->delete();
                            StockAvailability::where('purchase_item_id', $value->purchase_item_id)->delete();
                        }
                    }

                    $receivedItem          = 0;
                    $receivedItemAmount    = 0;
                    $notReceivedItem       = 0;
                    $notReceivedItemAmount = 0;
                    $totalRetur            = 0;
                    $totalReturAmount      = 0;
                    PurchaseItemReception::where('purchase_item_id', $value->purchase_item_id)->delete();
                    $arrItemReception = $req->input('purchase_item_reception_'.$value->purchase_item_id);
                    $arrFileReception = $req->file('purchase_item_reception_'.$value->purchase_item_id);
                    if ($req->has('purchase_item_reception_'.$value->purchase_item_id)) {
                        foreach ($arrItemReception as $k => $v) {
                            $dateTime     = $arrItemReception[$k]['date'];
                            $receivedDate = date('Y-m-d H:i', strtotime($dateTime));
                            $travelDoc    = null;
                            if (isset($arrItemReception[$k]['travel_number_document'])) {
                                $travelDoc = $arrItemReception[$k]['travel_number_document'];
                            } elseif (isset($arrFileReception[$k]['travel_number_document'])) {
                                $file = $arrFileReception[$k]['travel_number_document'];
                                if ($file) {
                                    $docUrl = FileHelper::upload($file, constants::PURCHASE_RECEPTION_DOC);
                                    if (! $docUrl['status']) {
                                        return redirect()->back()->with('error', 'Gagal upload');
                                    }
                                    $travelDoc = $docUrl['url'];
                                }
                            }
                            $received = str_replace('.', '', str_replace(',', '.', $arrItemReception[$k]['total_received']));
                            $retur    = str_replace('.', '', str_replace(',', '.', $arrItemReception[$k]['total_retur']));
                            $receivedItem += $received;
                            $receivedItemAmount = $receivedItem * $value->price;
                            $totalRetur += $retur;

                            $saveReceivedItem                         = new PurchaseItemReception;
                            $saveReceivedItem->purchase_item_id       = $value->purchase_item_id;
                            $saveReceivedItem->warehouse_id           = $arrItemReception[$k]['warehouse_id'];
                            $saveReceivedItem->received_date          = $receivedDate;
                            $saveReceivedItem->travel_number          = $arrItemReception[$k]['travel_number'];
                            $saveReceivedItem->travel_number_document = $travelDoc;
                            $saveReceivedItem->vehicle_number         = $arrItemReception[$k]['vehicle_number'];
                            $saveReceivedItem->total_received         = $received;
                            $saveReceivedItem->total_retur            = $retur;
                            $saveReceivedItem->supplier_id            = $arrItemReception[0]['supplier_id'];
                            $saveReceivedItem->transport_per_item     = str_replace('.', '', str_replace(',', '.', $arrItemReception[$k]['transport_per_item']));
                            $saveReceivedItem->transport_total        = str_replace('.', '', str_replace(',', '.', $arrItemReception[$k]['transport_total']));
                            $saveReceivedItem->save();
                            $warehouseReception = Warehouse::with('kandang.location')->find($arrItemReception[$k]['warehouse_id']);
                            $nonstock           = Nonstock::where('name', 'like', '%ekspedisi%')->first();
                            if ($saveReceivedItem->transport_total > 0) {
                                $arrKandang[] = [
                                    'location_id'         => $warehouseReception->location_id,
                                    'kandang_id'          => ($warehouseReception && $warehouseReception->type === 2) ? $warehouseReception->kandang_id : null,
                                    'supplier_id'         => $arrItemReception[0]['supplier_id'],
                                    'expense_main_prices' => [
                                        'supplier_id' => $arrItemReception[0]['supplier_id'],
                                        'nonstock_id' => $nonstock->nonstock_id,
                                        'qty'         => $arrItemReception[$k]['total_received'],
                                        'price'       => $arrItemReception[$k]['transport_total'],
                                        'notes'       => 'Auto generated by system '.$purchase->po_number,
                                    ],
                                ];
                            }

                            $adjusmentStock = $received - $retur;
                            $triggerStock   = StockLog::triggerStock([
                                'product_id'                 => $value->product_id,
                                'stock_date'                 => date('Y-m-d', strtotime($receivedDate)),
                                'warehouse_id'               => $arrItemReception[$k]['warehouse_id'],
                                'increase'                   => $adjusmentStock,
                                'stocked_by'                 => 'Pembelian',
                                'notes'                      => $arrItemReception[$k]['travel_number'],
                                'purchase_item_id'           => $value->purchase_item_id,
                                'purchase_item_reception_id' => $saveReceivedItem->purchase_item_reception_id,
                            ]);

                            if (! $triggerStock['result']) {
                                DB::rollback();

                                return redirect()->back()->with('error', $triggerStock['message']);
                            }
                        }

                        if ($receivedItem > $value->qty) {
                            DB::rollback();

                            return redirect()->back()->with('error', 'Jumlah produk yang diterima tidak boleh melebihi jumlah produk yang dipesan');
                        }
                        $notReceivedItem       = $value->qty - $receivedItem;
                        $notReceivedItemAmount = $value->price * $notReceivedItem;
                        PurchaseItem::where('purchase_item_id', $value->purchase_item_id)->update([
                            'total_not_received'  => $notReceivedItem,
                            'amount_not_received' => $notReceivedItemAmount,
                            'total_received'      => $receivedItem,
                            'amount_received'     => $receivedItemAmount,
                        ]);
                    } else {
                        DB::rollback();

                        return redirect()->back()->with('error', 'Penerimaan produk harus diisi');
                    }

                    $receivedPurchaseAmount    += $receivedItemAmount;
                    $notReceivedPurchaseAmount += $notReceivedItemAmount;
                    $returPurchaseAmount       += $totalRetur * $value->price;
                    $purchase->update([
                        'total_amount_received'     => $receivedPurchaseAmount,
                        'total_amount_not_received' => $notReceivedPurchaseAmount,
                        'total_amount_retur'        => $returPurchaseAmount,
                    ]);
                }
                if (count($arrKandang) > 0 && ! $req->has('save_only')) {
                    $rawExpense = collect($arrKandang)
                        ->groupBy('location_id')
                        ->map(function($items) {
                            return [
                                'location_id'         => $items->first()['location_id'],
                                'supplier_id'         => $items->first()['supplier_id'],
                                'kandang_id'          => $items->pluck('kandang_id')->filter()->unique()->values()->all(),
                                'expense_main_prices' => $items->pluck('expense_main_prices')->values()->all(),
                            ];
                        })
                        ->values()
                        ->all();

                    foreach ($rawExpense as $key => $value) {
                        $expenseEvent = Expense::expenseEvent([
                            'purchase_id'         => $purchase->purchase_id,
                            'location_id'         => $value['location_id'],
                            'supplier_id'         => $value['supplier_id'],
                            'kandangs'            => $value['kandang_id'],
                            'trx_date'            => $purchase->po_date,
                            'expense_main_prices' => $value['expense_main_prices'],
                            'po_number'           => $purchase->po_number,
                        ]);

                        if (! $expenseEvent['success']) {
                            \Log::info('expenseEvent purchase '.json_encode($expenseEvent));
                            DB::rollback();

                            return redirect()->back()->with('error', 'Terjadi masalah pada sistem, silahkan hubungi departemen IT');
                        }
                    }
                }
            }

            if (in_array($arrPurchaseStatus[$purchase->status], ['Approval Finance', 'Approval Dir. Finance'])) {
                $arrPrNumber               = explode('-', $purchase->pr_number);
                $companyAlias              = $arrPrNumber[1];
                $incrementNumber           = $arrPrNumber[2];
                $dataApproval['po_number'] = 'PO-'.$companyAlias.'-'.$incrementNumber;
                $dataApproval['po_date']   = date('Y-m-d');
            }

            if ($arrPurchaseStatus[$purchase->status] == 'Dibayar Sebagian') {
                PurchasePayment::where('purchase_id', $req->id)->delete();
            }

            if ($req->has('purchase_item')) {
                $arrPurchaseItem                  = $req->input('purchase_item');
                $dataApproval['total_before_tax'] = 0;
                $dataApproval['total_discount']   = 0;
                $dataApproval['total_tax']        = 0;
                foreach ($arrPurchaseItem as $key => $value) {
                    $idPurchaseItem = $key;
                    $itemPrice      = str_replace('.', '', $value['price']);
                    $itemQty        = str_replace('.', '', $value['qty']);
                    $value['qty']   = $itemQty;
                    $dataApproval['total_before_tax'] += $itemPrice              * $itemQty;
                    $dataApproval['total_tax']        += ($itemPrice * $itemQty) * $value['tax']      / 100;
                    $dataApproval['total_discount']   += ($itemPrice * $itemQty) * $value['discount'] / 100;
                    $value['price']     = $itemPrice;
                    $updatePurchaseItem = PurchaseItem::find($idPurchaseItem);
                    $updatePurchaseItem->update($value);
                }

                $totalItem                               = $dataApproval['total_before_tax'] + $dataApproval['total_tax'] - $dataApproval['total_discount'];
                $dataApproval['total_after_tax']         = $totalItem;
                $dataApproval['grand_total']             = $totalItem;
                $dataApproval['total_remaining_payment'] = $dataApproval['grand_total'] - $purchase->total_payment;
                $purchase->update($dataApproval);
            }

            if ($req->has('purchase_other')) {
                $arrOtherAmount                     = $req->input('purchase_other');
                $dataApproval['total_other_amount'] = 0;
                for ($i = 0; $i < count($arrOtherAmount); $i++) {
                    if ($arrOtherAmount[$i]['name'] && $arrOtherAmount[$i]['amount']) {
                        $otherAmount = str_replace('.', '', $arrOtherAmount[$i]['amount']);
                        $dataApproval['total_other_amount'] += (int) $otherAmount;
                        PurchaseOther::create([
                            'purchase_id' => $req->id,
                            'name'        => $arrOtherAmount[$i]['name'],
                            'amount'      => (int) $otherAmount,
                        ]);
                    }
                }
                $currentItemAmount                       = $purchase->total_before_tax         + $purchase->total_tax - $purchase->total_discount;
                $dataApproval['grand_total']             = $dataApproval['total_other_amount'] + $currentItemAmount;
                $dataApproval['total_remaining_payment'] = $dataApproval['grand_total'] - $purchase->total_payment;
            }

            $purchase->update($dataApproval);
            DB::commit();

            $success = ['success' => $successMsg];

            return redirect()->route('purchase.detail', $req->id)->with($success);
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function payment(Request $req)
    {
        try {
            $purchase = Purchase::with('purchase_payment')->findOrFail($req->id);
            $data     = [];
            if ($req->query('purchase_payment_id')) {
                $data = PurchasePayment::with(['own_bank', 'recipient_bank'])
                    ->findOrFail($req->input('purchase_payment_id'));

                return json_encode($data);
            }

            if ($req->isMethod('post')) {
                DB::beginTransaction();
                if ($req->has('method') && $req->input('method') == 'put') {
                    $data          = PurchasePayment::findOrFail($req->input('purchase_payment_id'));
                    $statusApprove = null;
                    $action        = $req->input('action');
                    if ($action == 'approved') {
                        $statusApprove = array_search('Disetujui', constants::PAYMENT_STATUS);
                        $data->update([
                            'status' => $statusApprove,
                        ]);
                        $currentPurchase = Purchase::with('purchase_payment')->findOrFail($req->id);
                        $remain          = 0;
                        foreach ($currentPurchase->purchase_payment as $key => $value) {
                            if ($value->status == $statusApprove) {
                                $remain += $value->amount;
                            }
                        }
                        $remainPayment = $purchase->grand_total - $remain;
                        $totalPayment  = $purchase->total_payment + $data->amount;
                        if ($remainPayment == 0) {
                            $currentApprLine = json_decode($purchase->approval_line);
                            $approvalLine    = [
                                'status'    => $this->getStatus('Dibayar Sebagian'),
                                'notes'     => null,
                                'action_by' => auth()->user()->name,
                                'date'      => date('Y-m-d H:i:s'),
                            ];
                            array_push($currentApprLine, $approvalLine);
                            $purchase->update([
                                'total_payment'           => $totalPayment,
                                'approval_line'           => $currentApprLine,
                                'total_remaining_payment' => $remainPayment,
                                'status'                  => $this->getStatus('Lunas'),
                            ]);
                        } else {
                            $purchase->update([
                                'total_payment'           => $totalPayment,
                                'total_remaining_payment' => $remainPayment,
                            ]);
                        }
                    } elseif ($action == 'reject') {
                        $statusApprove = array_search('Ditolak', constants::PAYMENT_STATUS);
                        $data->update([
                            'status' => $statusApprove,
                        ]);
                    }
                } elseif ($req->has('method') && $req->input('method') == 'delete') {
                    $data = PurchasePayment::findOrFail($req->input('purchase_payment_id'));
                    $purchase->update([
                        'total_payment'           => $purchase->total_payment - $data->amount,
                        'total_remaining_payment' => $purchase->total_remaining_payment + $data->amount,
                    ]);
                    $data->delete();
                } else {
                    $document = '';
                    if ($req->has('document')) {
                        $docUrl = FileHelper::upload($req->file('document'), constants::PAYMENT_DOC);
                        if (! $docUrl['status']) {
                            return redirect()->back()->with('error', $docUrl['message'])->withInput();
                        }
                        $document = $docUrl['url'];
                    }

                    $bankCharge    = str_replace('.', '', str_replace(',', '.', $req->input('bank_charge')));
                    $amountPayment = str_replace('.', '', str_replace(',', '.', $req->input('amount')));
                    if ($amountPayment > $purchase->total_remaining_payment) {
                        DB::rollback();

                        return redirect()->back()->with('error', 'Error, Total pembayaran melebihi sisa pembayaran');
                    }

                    PurchasePayment::create([
                        'purchase_id'        => $req->input('id'),
                        'payment_date'       => date('Y-m-d', strtotime($req->input('payment_date'))),
                        'payment_method'     => $req->input('payment_method'),
                        'own_bank_id'        => $req->input('own_bank_id'),
                        'recipient_bank_id'  => $req->input('recipient_bank_id'),
                        'ref_number'         => $req->input('ref_number'),
                        'transaction_number' => $req->input('transaction_number'),
                        'bank_charge'        => $bankCharge,
                        'amount'             => $amountPayment,
                        'document'           => $document,
                        'status'             => array_search('Menunggu persetujuan', Constants::PAYMENT_STATUS),
                    ]);
                }
                DB::commit();
            }

            $success = ['success' => 'Data berhasil diperbaharui'];

            return redirect()->route('purchase.detail', $req->id)->with($success);
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req)
    {
        try {
            $purchase = Purchase::findOrFail($req->id);
            $purchase->delete();

            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('purchase.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchProject(Request $request)
    {
        $search      = $request->input('q');
        $projects    = Purchase::with('kandang')->where('created_at', 'like', "%{$search}%");
        $queryParams = $request->query();
        foreach ($queryParams as $key => $value) {
            $projects->where($key, $value);
        }

        $projects = $projects->get();

        return response()->json($projects->map(function($project) {
            return ['id' => $project->project_id, 'text' => $project->kandang->name.' - '.date('d-M-Y', strtotime($project->created_at))];
        }));
    }
}
