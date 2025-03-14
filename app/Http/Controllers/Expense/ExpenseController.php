<?php

namespace App\Http\Controllers\Expense;

use App\Constants;
use App\Helpers\FileHelper;
use App\Helpers\Parser;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Kandang;
use App\Models\DataMaster\Location;
use App\Models\Expense\Expense;
use App\Models\Expense\ExpenseAdditPrice;
use App\Models\Expense\ExpenseKandang;
use App\Models\Expense\ExpenseMainPrice;
use App\Models\Expense\ExpenseRealization;
use App\Models\Expense\ExpenseReturnPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index()
    {
        try {
            $currentUserId = auth()->id();

            $data = Expense::with(['location', 'expense_disburses', 'created_user', 'expense_main_prices.nonstock'])
                ->where(function($query) use ($currentUserId) {
                    $query->where('expense_status', '!=', 0)
                        ->orWhere(function($subQuery) use ($currentUserId) {
                            $subQuery->where('expense_status', 0)
                                ->whereHas('created_user', function($userQuery) use ($currentUserId) {
                                    $userQuery->where('user_id', $currentUserId);
                                });
                        });
                })
                ->get();

            $param = [
                'title' => 'Biaya > List',
                'data'  => $data,
            ];

            return view('expense.list.index', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function recap(Request $req)
    {
        try {
            $input         = $req->query();
            $bop_items     = null;
            $non_bop_items = null;

            if (count($input) > 0) {
                $query = Expense::with([
                    'expense_main_prices.nonstock',
                    'expense_addit_prices',
                    'expense_kandang.kandang',
                    'expense_disburses',
                ])
                    ->whereBetween('created_at', [
                        $input['date_start'], $input['date_end'] ?? now(),
                    ]);

                $location = null;
                if (isset($input['location_id'])) {
                    $location = Location::find($input['location_id']);
                    $query->where('location_id', $location->location_id);
                }

                $selectedFarms = json_decode($req->input('farms', '[]'));

                if (! empty($selectedFarms)) {
                    $query->whereHas(
                        'expense_kandang.kandang',
                        function($q) use ($selectedFarms) {
                            $q->whereIn('kandang_id', $selectedFarms);
                        }
                    );
                }

                $bop = (clone $query)->where('category', 1)->get();
                if (count($bop) > 0) {
                    $bop_items = $bop->flatMap(function($eb) use ($selectedFarms) {
                        $bop_kandangs = ! empty($selectedFarms)
                            ? $eb->expense_kandang->whereIn('kandang_id', $selectedFarms)
                            : [1];

                        // For grouping by kandang
                        $cloned_items = collect();

                        // For when not filtered by kandang, assign all the kandang names to the item
                        $kandangs = [];
                        if (empty($selectedFarms)) {
                            $kandangs = $eb->expense_kandang->pluck('kandang.name')->toArray();
                        }

                        foreach ($bop_kandangs as $e_kandang) {
                            $main_prices = $eb->expense_main_prices->map(function($mp) use (&$bop_is_paid, $e_kandang, $kandangs, $selectedFarms) {
                                $new_mp = clone $mp; // Clone the price item to avoid overwrite

                                $new_mp->id_expense    = $new_mp->expense->id_expense;
                                $new_mp->location_name = $new_mp->expense->location->name;
                                $new_mp->created_at    = $new_mp->expense->created_at;
                                $new_mp->status        = $new_mp->expense->payment_status;

                                if (empty($selectedFarms)) {
                                    $new_mp->kandangs = $kandangs;
                                } else {
                                    $new_mp->kandangs = [$e_kandang->kandang->name];
                                }

                                return $new_mp;
                            });

                            // Clone additional prices
                            $addit_prices = $eb->expense_addit_prices->map(function($ap) use (&$bop_is_paid, $e_kandang, $kandangs, $selectedFarms) {
                                $new_ap = clone $ap; // Clone the price item to avoid overwrite

                                $new_ap->id_expense    = $new_ap->expense->id_expense;
                                $new_ap->location_name = $new_ap->expense->location->name;
                                $new_ap->created_at    = $new_ap->expense->created_at;
                                $new_ap->status        = $new_ap->expense->payment_status;

                                if (empty($selectedFarms)) {
                                    $new_ap->kandangs = $kandangs;
                                } else {
                                    $new_ap->kandangs = [$e_kandang->kandang->name];
                                }

                                return $new_ap;
                            });

                            $cloned_items = $cloned_items->concat($main_prices)->concat($addit_prices);
                        }

                        return $cloned_items;
                    });
                }

                $non_bop = (clone $query)->where('category', 2)->get();
                if (count($non_bop) > 0) {
                    $non_bop_items = $non_bop->flatMap(function($eb) {
                        $main_prices = $eb->expense_main_prices->map(function($mp) use (&$non_bop_is_paid) {
                            $mp->id_expense    = $mp->expense->id_expense;
                            $mp->location_name = $mp->expense->location->name;
                            $mp->created_at    = $mp->expense->created_at;
                            $mp->status        = $mp->expense->payment_status;

                            return $mp;
                        });

                        $addit_prices = $eb->expense_addit_prices->map(function($ap) use (&$non_bop_is_paid) {
                            $ap->id_expense    = $ap->expense->id_expense;
                            $ap->location_name = $ap->expense->location->name;
                            $ap->created_at    = $ap->expense->created_at;
                            $ap->status        = $ap->expense->payment_status;

                            return $ap;
                        });

                        return $main_prices->concat($addit_prices);
                    });
                }
            }

            $old                  = $req->query();
            $old['location_id']   = isset($location) ? $location->location_id : null;
            $old['location_name'] = isset($location) ? $location->name : null;
            $old['kandangs']      = isset($location) ? Kandang::where('location_id', $location->location_id)->select('kandang_id', 'name', 'project_status')->get() : null;
            if (isset($bop_items)) {
                $old['available_kandangs'] = $bop_items->pluck('kandangs')->flatten()->unique()->values()
                    ->sortBy(function($item) {
                        preg_match('/\d+$/', $item, $matches);

                        return (int) $matches[0];
                    })
                    ->all();
            }

            $param = [
                'title' => 'Biaya > List',
                'data'  => [
                    'bop'     => $bop_items,
                    'non_bop' => $non_bop_items,
                ],
                'old' => $old,
            ];

            if (isset($input['print']) && $input['print'] == true) {
                return view('expense.recap.download-recap', $param);
            }

            return view('expense.recap.index', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function add(Request $req)
    {
        try {
            $param = [
                'title' => 'Biaya > Tambah',
            ];

            if ($req->isMethod('post')) {
                $input = $req->all();

                if (! $req->has('expense_main_prices')) {
                    return redirect()->back()->with('error', 'Biaya Utama tidak boleh kosong')->withInput($input);
                }

                $expenseID = 0;
                $success   = DB::transaction(function() use ($req, $input, &$expenseID) {
                    $category      = $input['category'];
                    $expenseStatus = $input['expense_status'];
                    $billPath      = '';

                    if (isset($input['bill_docs'])) {
                        $docUrl = FileHelper::upload($input['bill_docs'], Constants::EXPENSE_BILL_DOC_PATH);
                        if (! $docUrl['status']) {
                            throw new \Exception($docUrl['message']);
                        }
                        $billPath = $docUrl['url'];
                    }

                    if ($expenseStatus == 0) {
                        // Save as Draft
                        $createdExpense = Expense::create([
                            'location_id'       => $input['location_id'],
                            'supplier_id'       => $input['supplier_id'] ?? null,
                            'category'          => $category,
                            'bill_docs'         => $billPath ?? null,
                            'realization_docs'  => null,
                            'transaction_date'  => $input['transaction_date'],
                            'payment_status'    => 0,
                            'expense_status'    => 0,
                            'created_by'        => Auth::id(),
                            'parent_expense_id' => $req->query('parent_expense_id') ? intval($req->query('parent_expense_id')) : null,
                        ]);

                        $expenseID = $createdExpense->expense_id;
                    } else {
                        $createdExpense = Expense::create([
                            'location_id'       => $input['location_id'],
                            'supplier_id'       => $input['supplier_id'] ?? null,
                            'category'          => $category,
                            'bill_docs'         => $billPath ?? null,
                            'realization_docs'  => null,
                            'transaction_date'  => $input['transaction_date'],
                            'payment_status'    => 1,
                            'expense_status'    => array_search('Approval Manager', Constants::EXPENSE_STATUS),
                            'created_by'        => Auth::id(),
                            'parent_expense_id' => $req->query('parent_expense_id') ? intval($req->query('parent_expense_id')) : null,
                        ]);

                        $expenseID = $createdExpense->expense_id;
                    }

                    $selectedKandangs = json_decode($req->input('selected_kandangs'), true);
                    $arrKandang       = [];
                    if (count($selectedKandangs) > 0) {
                        $create = false;

                        foreach ($selectedKandangs as $key => $value) {
                            if ($category == 1) {
                                $create                         = true;
                                $arrKandang[$key]['expense_id'] = $expenseID;
                                $arrKandang[$key]['kandang_id'] = $value;

                                // assign project_id
                                $project = Kandang::find($value)->project->where('project_status', '!=', 4)->first() ?? null;
                                if ($project) {
                                    $arrKandang[$key]['project_id'] = $project->project_id;
                                }
                            }
                        }
                        ExpenseKandang::insert($arrKandang);
                    }

                    if ($req->has('expense_main_prices')) {
                        $arrMainPrices = $req->input('expense_main_prices');

                        foreach ($arrMainPrices as $key => $value) {
                            $qty        = Parser::parseLocale($value['qty']);
                            $totalPrice = Parser::parseLocale($value['price']);

                            $arrMainPrices[$key]['expense_id']  = $expenseID;
                            $arrMainPrices[$key]['nonstock_id'] = $value['nonstock_id'];
                            $arrMainPrices[$key]['supplier_id'] = $input['supplier_id'] ?? null;
                            $arrMainPrices[$key]['qty']         = $qty;
                            $arrMainPrices[$key]['price']       = $totalPrice;
                            $arrMainPrices[$key]['notes']       = $value['notes'] ?? null;

                            ExpenseMainPrice::create($arrMainPrices[$key]);
                        }

                        // ExpenseMainPrice::insert($arrMainPrices);
                    }

                    if ($req->has('expense_addit_prices')) {
                        $arrAdditPrices = $req->input('expense_addit_prices');
                        $create         = false;

                        foreach ($arrAdditPrices as $key => $value) {
                            $name  = $value['name'] ?? null;
                            $price = Parser::parseLocale($value['price'] ?? null);

                            if ($name && $price) {
                                $create                             = true;
                                $arrAdditPrices[$key]['expense_id'] = $expenseID;
                                $arrAdditPrices[$key]['name']       = $name;
                                $arrAdditPrices[$key]['price']      = $price;
                                $arrAdditPrices[$key]['notes']      = $value['notes'] ?? null;
                            }

                            ExpenseAdditPrice::create($arrAdditPrices[$key]);
                        }
                        // if ($create) {
                        //     ExpenseAdditPrice::insert($arrAdditPrices);
                        // }
                    }

                    if ($expenseStatus == 1) {
                        $prefix      = $category == 1 ? 'BOP' : 'NBOP';
                        $incrementId = Expense::where('id_expense', 'LIKE', "{$prefix}.%")->withTrashed()->count() + 1;
                        $idExpense   = "{$prefix}.{$incrementId}";

                        $createdExpense->update([
                            'id_expense' => $idExpense,
                        ]);
                    }

                    // Success message according to project_id
                    if (! empty($arrKandang)) {
                        $projectIds       = array_column($arrKandang, 'project_id');
                        $projectIdsString = implode(', ', $projectIds);

                        return ['success' => "Biaya Berhasil Disimpan | Terhubung Pada Project ID {$projectIdsString}"];
                    } else {
                        return ['success' => 'Biaya Berhasil Disimpan | Tidak Terhubung Pada Project'];
                    }
                });

                return redirect()
                    ->route('expense.list.index')
                    ->with($success);
            }

            return view('expense.list.add', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function detail(Request $req, Expense $expense)
    {
        try {
            $data = $expense->load([
                'created_user',
                'location',
                'expense_kandang.kandang',
                'expense_main_prices',
                'expense_addit_prices',
                'expense_disburses',
                'expense_return.bank',
            ]);

            $param = [
                'title'          => 'Biaya > Detail',
                'data'           => $data,
                'expense_status' => Constants::EXPENSE_STATUS,
            ];

            // if ($req->has('po_number')) {
            //     if ($req->query('po_number') == $expense->po_number) {
            //         return view('expense.list.po', $param);
            //     }
            // }

            return view('expense.list.detail', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Request $req, Expense $expense)
    {
        try {
            $data = $expense->load([
                'created_user',
                'location',
                'expense_kandang',
                'expense_main_prices',
                'expense_addit_prices',
            ]);

            $param = [
                'title' => 'Biaya > Edit',
                'data'  => $data,
            ];

            if ($req->isMethod('post')) {
                $input = $req->all();

                if (! $req->has('expense_main_prices')) {
                    return redirect()->back()->with('error', 'Biaya Utama tidak boleh kosong')->withInput($input);
                }

                $success = DB::transaction(function() use ($req, $input, $expense, $data) {
                    $existingBillPath = $data->bill_docs ?? null;
                    $billPath         = '';

                    if (isset($input['bill_docs'])) {
                        $docUrl = FileHelper::upload($input['bill_docs'], Constants::EXPENSE_BILL_DOC_PATH);
                        if (! $docUrl['status']) {
                            throw new \Exception($docUrl['message']);
                        }
                        $billPath = $docUrl['url'];
                    } else {
                        $billPath = $existingBillPath;
                    }

                    if ($expense->expense_status == 0) {
                        $expense->update([
                            'location_id'      => $input['location_id'],
                            'bill_docs'        => $billPath,
                            'transaction_date' => $input['transaction_date'],
                            'payment_status'   => 1,
                            'expense_status'   => array_search('Approval Manager', Constants::EXPENSE_STATUS),
                        ]);
                    }

                    $expense->expense_kandang()->delete();
                    $selectedKandangs = json_decode($input['selected_kandangs'], true);
                    $arrKandang       = [];
                    if (count($selectedKandangs) > 0) {
                        $create = false;

                        foreach ($selectedKandangs as $key => $value) {
                            if ($expense->category == 1) {
                                $create                         = true;
                                $arrKandang[$key]['expense_id'] = $expense->expense_id;
                                $arrKandang[$key]['kandang_id'] = $value;

                                // assign project_id
                                $project = Kandang::find($value)->project->where('project_status', '!=', 4)->first() ?? null;
                                if ($project) {
                                    $arrKandang[$key]['project_id'] = $project->project_id;
                                }
                            }
                        }
                        ExpenseKandang::insert($arrKandang);
                    }

                    if ($req->has('expense_main_prices')) {
                        $expense->expense_main_prices()->delete();
                        $arrMainPrices = $req->input('expense_main_prices');

                        foreach ($arrMainPrices as $key => $value) {
                            $qty        = Parser::parseLocale($value['qty']);
                            $totalPrice = Parser::parseLocale($value['price']);

                            $arrMainPrices[$key]['expense_id']  = $expense->expense_id;
                            $arrMainPrices[$key]['nonstock_id'] = $value['nonstock_id'];
                            $arrMainPrices[$key]['supplier_id'] = $input['supplier_id'] ?? null;
                            $arrMainPrices[$key]['qty']         = $qty;
                            $arrMainPrices[$key]['price']       = $totalPrice;
                            $arrMainPrices[$key]['notes']       = $value['notes'] ?? null;

                            ExpenseMainPrice::create($arrMainPrices[$key]);
                        }

                        // ExpenseMainPrice::insert($arrMainPrices);
                    }

                    $expense->expense_addit_prices()->delete();
                    if ($req->has('expense_addit_prices')) {
                        $arrAdditPrices = $req->input('expense_addit_prices');
                        $create         = false;

                        foreach ($arrAdditPrices as $key => $value) {
                            $name  = $value['name'] ?? null;
                            $price = Parser::parseLocale($value['price'] ?? null);

                            if ($name && $price) {
                                $create                             = true;
                                $arrAdditPrices[$key]['expense_id'] = $expense->expense_id;
                                $arrAdditPrices[$key]['name']       = $name;
                                $arrAdditPrices[$key]['price']      = $price;
                                $arrAdditPrices[$key]['notes']      = $value['notes'] ?? null;
                            }

                            ExpenseAdditPrice::create($arrAdditPrices[$key]);
                        }
                        // if ($create) {
                        //     ExpenseAdditPrice::insert($arrAdditPrices);
                        // }
                    }

                    if (! empty($expense->id_expense)) {
                        $prefix      = $expense->category == 1 ? 'BOP' : 'NBOP';
                        $incrementId = Expense::where('id_expense', 'LIKE', "{$prefix}.%")->withTrashed()->count() + 1;
                        $idExpense   = "{$prefix}.{$incrementId}";

                        $expense->update([
                            'id_expense' => $idExpense,
                        ]);
                    }

                    if (! empty($arrKandang)) {
                        $projectIds       = array_column($arrKandang, 'project_id');
                        $projectIdsString = implode(', ', $projectIds);

                        return ['success' => "Biaya Berhasil Disimpan | Terhubung Pada Project ID {$projectIdsString}"];
                    } else {
                        return ['success' => 'Biaya Berhasil Disimpan | Tidak Terhubung Pada Project'];
                    }
                });

                return redirect()
                    ->route('expense.list.index')
                    ->with($success);
            }

            return view('expense.list.edit', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function realization(Request $req, Expense $expense)
    {
        try {
            $data = $expense->load([
                'created_user',
                'location',
                'expense_main_prices',
                'expense_addit_prices',
                'expense_realizations.expense_main_price.nonstock.uom',
                'expense_realizations.expense_addit_price',
            ]);

            $param = [
                'title' => 'Biaya > Realisasi',
                'data'  => $data,
                'main'  => $data->expense_realizations()->whereNotNull('expense_item_id')->with('expense_main_price.nonstock.uom')->get(),
                'addit' => $data->expense_realizations()->whereNotNull('expense_addit_price_id')->with('expense_addit_price')->get(),
            ];

            if ($req->isMethod('post')) {
                $input = $req->all();

                if (! $req->has('realization_main_prices')) {
                    return redirect()->back()->with('error', 'Biaya Utama tidak boleh kosong')->withInput($input);
                }

                $success = DB::transaction(function() use ($req, $input, $expense, $data) {
                    $existingRealizationPath = $data->realization_docs ?? null;
                    $realizationPath         = '';

                    if (isset($input['realization_docs'])) {
                        $docUrl = FileHelper::upload($input['realization_docs'], constants::EXPENSE_REALIZATION_DOC_PATH);
                        if (! $docUrl['status']) {
                            throw new \Exception($docUrl['message']);
                        }
                        $realizationPath = $docUrl['url'];
                    } else {
                        $realizationPath = $existingRealizationPath;
                    }

                    $expense->update([
                        'realization_docs' => $realizationPath,
                    ]);

                    if ($req->has('realization_main_prices')) {
                        $arrRealizationMainPrices = $req->input('realization_main_prices');

                        foreach ($arrRealizationMainPrices as $key => $value) {
                            $qty   = Parser::parseLocale($value['qty']);
                            $price = Parser::parseLocale($value['price']);

                            ExpenseRealization::find($key)->update([
                                'qty'   => $qty,
                                'price' => $price,
                            ]);
                        }
                    }

                    if ($req->has('realization_addit_prices')) {
                        $arrRealizationAdditPrices = $req->input('realization_addit_prices');

                        foreach ($arrRealizationAdditPrices as $key => $value) {
                            $price = Parser::parseLocale($value['price']);

                            ExpenseRealization::find($key)->update([
                                'price' => $price,
                            ]);
                        }
                    }

                    return ['success' => 'Realisasi Biaya Berhasil Disimpan'];
                });

                return redirect()
                    ->route('expense.list.detail', ['expense' => $expense->expense_id, 'page' => 'realization'])
                    ->with($success);
            }

            return view('expense.list.realization', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function finish(Expense $expense)
    {
        try {
            if ($expense->grand_total !== $expense->is_realized && $expense->is_paid === $expense->is_realized) {
                throw new \Exception('Belum bisa diselesaikan. Nominal Pencairan dan Realisasi belum sesuai!');
            }

            $expense->update([
                'expense_status' => array_search('Selesai', Constants::EXPENSE_STATUS),
            ]);

            $success = ['success' => 'Biaya berhasil diselesaikan'];

            return redirect()->route('expense.list.detail', ['expense' => $expense->expense_id])->with($success);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function returnPayment(Request $req, Expense $expense)
    {
        try {
            DB::transaction(function() use ($req, $expense) {
                $input = $req->all();

                $docPath = '';
                if ($req->hasFile('return_docs')) {
                    $docUrl = FileHelper::upload($input['return_docs'], Constants::EXPENSE_DISBURSE_DOC_PATH);
                    if (! $docUrl['status']) {
                        return redirect()->back()->with('error', $docUrl['message'].' '.$input['return_docs'])->withInput();
                    }
                    $docPath = $docUrl['url'];
                }

                ExpenseReturnPayment::updateOrCreate(
                    [
                        'expense_id' => $expense->expense_id,
                    ],
                    [
                        'expense_id'         => $expense->expense_id,
                        'payment_method'     => $input['payment_method'],
                        'bank_id'            => $input['bank_id']           ?? null,
                        'bank_recipient_id'  => $input['bank_recipient_id'] ?? null,
                        'payment_reference'  => $input['payment_reference'],
                        'transaction_number' => $input['transaction_number'],
                        'payment_nominal'    => Parser::parseLocale($input['payment_nominal']),
                        'bank_admin_fees'    => Parser::parseLocale($input['bank_admin_fees']),
                        'payment_at'         => date('Y-m-d', strtotime($input['payment_at'])),
                        'return_docs'        => $docPath,
                        'notes'              => $input['notes'],
                    ]
                );
            });

            $success = ['success' => 'Data Berhasil disimpan'];

            return redirect()
                ->back()
                ->with($success);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function delete(Expense $expense)
    {
        try {
            if (empty($expense)) {
                throw new \Exception('Biaya tidak ditemukan');
            }
            $expense->delete();
            $success = ['success' => 'Data Berhasil dihapus'];

            return redirect()->route('expense.list.index')->with($success);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function approveBulk(Request $req)
    {
        DB::beginTransaction();
        try {
            if (! $req->has('farm_expense_ids') && ! $req->has('finance_expense_ids')) {
                return redirect()->back()->with('error', 'Pilih Biaya terlebih dahulu');
            }

            $approvedExpenseIds = [];

            // Manager Farm
            if ($req->has('farm_expense_ids')) {
                $arrExpenseIds = $req->input('farm_expense_ids');
                foreach ($arrExpenseIds as $expenseId) {
                    $expense       = Expense::findOrFail($expenseId);
                    $expenseStatus = array_search('Approval Finance', Constants::EXPENSE_STATUS);
                    if ($expense->expense_status !== $expenseStatus - 1) {
                        return redirect()->back()->with('error', "Biaya $expense->id_expense belum bisa disetujui oleh Manager Farm");
                    }

                    $increment           = str_pad($expense->expense_id + 1, 5, '0', STR_PAD_LEFT);
                    $category            = $expense->category == 1 ? 'BOP' : 'NBOP';
                    $currentApprovalLine = json_decode($expense->approval_line ?? '[]');
                    $approvedAt          = date('Y-m-d H:i:s');

                    array_push($currentApprovalLine, [
                        'status'      => $expenseStatus - 1,
                        'is_approved' => 1,
                        'notes'       => 'System Generated - Bulk Approved',
                        'action_by'   => Auth::user()->name,
                        'date'        => $approvedAt,
                    ]);

                    $expense->update([
                        'is_approved'    => 1,
                        'po_number'      => $expense->po_number ?: "PO-{$expense->created_user->role->company->alias}-{$category}-{$increment}",
                        'expense_status' => $expenseStatus,
                        'approval_line'  => $currentApprovalLine,
                    ]);

                    array_push($approvedExpenseIds, $expense->id_expense);
                }
            }

            // Manager Finance
            if ($req->has('finance_expense_ids')) {
                $arrExpenseIds = $req->input('finance_expense_ids');
                foreach ($arrExpenseIds as $expenseId) {
                    $expense       = Expense::findOrFail($expenseId);
                    $expenseStatus = array_search('Pencairan', Constants::EXPENSE_STATUS);
                    if ($expense->expense_status !== $expenseStatus - 1) {
                        return redirect()->back()->with('error', "Biaya $expense->id_expense belum bisa disetujui oleh Manager Finance");
                    }

                    $increment           = str_pad($expense->expense_id + 1, 5, '0', STR_PAD_LEFT);
                    $category            = $expense->category == 1 ? 'BOP' : 'NBOP';
                    $currentApprovalLine = json_decode($expense->approval_line ?? '[]');
                    $approvedAt          = date('Y-m-d H:i:s');

                    array_push($currentApprovalLine, [
                        'status'      => $expenseStatus - 1,
                        'is_approved' => 1,
                        'notes'       => 'System Generated - Bulk Approved',
                        'action_by'   => Auth::user()->name,
                        'date'        => $approvedAt,
                    ]);

                    $expense->update([
                        'is_approved'    => 1,
                        'po_number'      => $expense->po_number ?: "PO-{$expense->created_user->role->company->alias}-{$category}-{$increment}",
                        'expense_status' => $expenseStatus,
                        'approval_line'  => $currentApprovalLine,
                    ]);

                    array_push($approvedExpenseIds, $expense->id_expense);
                }
            }

            DB::commit();

            $stringApprovedExpenseIds = implode(', ', $approvedExpenseIds);
            $success                  = ['success' => "Biaya $stringApprovedExpenseIds berhasil disetujui"];

            return redirect()->route('expense.list.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function approve(Request $req, Expense $expense)
    {
        DB::beginTransaction();
        try {
            $input = $req->all();

            $success             = ['success' => 'Biaya berhasil ditolak'];
            $approvedAt          = null;
            $expenseStatus       = $expense->expense_status;
            $currentApprovalLine = json_decode($expense->approval_line ?? '[]');

            $routeName = $req->route()->getName();

            if ($input['is_approved'] == 1) {

                // Approval Manager Farm
                if (str_contains($routeName, 'farm')) {
                    // Ganti status jadi menunggu Approval Finance
                    $expenseStatus = array_search('Approval Finance', Constants::EXPENSE_STATUS);
                }

                // Approval Manager Finance
                if (str_contains($routeName, 'finance')) {
                    // error if not the right step
                    if ($expense->expense_status !== array_search('Approval Finance', Constants::EXPENSE_STATUS)) {
                        throw new \Exception('Belum disetujui oleh Manager Farm');
                    }

                    // Ganti status jadi menunggu Pencairan
                    $expenseStatus = array_search('Pencairan', Constants::EXPENSE_STATUS);
                }

                $success = ['success' => 'Biaya berhasil disetujui'];
            }

            $increment  = str_pad($expense->expense_id + 1, 5, '0', STR_PAD_LEFT);
            $category   = $expense->category == 1 ? 'BOP' : 'NBOP';
            $approvedAt = date('Y-m-d H:i:s');

            array_push($currentApprovalLine, [
                'status'      => $expenseStatus - ($input['is_approved'] == 1 ? 1 : 0),
                'is_approved' => $input['is_approved'] == 1 ? 1 : 0,
                'notes'       => $input['approval_notes'],
                'action_by'   => Auth::user()->name,
                'date'        => $approvedAt,
            ]);

            $expense->update([
                'is_approved'    => $input['is_approved'] == 1 ? 1 : 0,
                'po_number'      => $expense->po_number ?: "PO-{$expense->created_user->role->company->alias}-{$category}-{$increment}",
                'expense_status' => $expenseStatus,
                'approval_line'  => $currentApprovalLine,
            ]);

            DB::commit();

            return redirect()->route('expense.list.index')->with($success);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchExpense(Request $req)
    {
        $search = $req->input('q');
        $query  = Expense::with(['location', 'created_user', 'expense_disburses'])
            ->where('id_expense', 'like', "%{$search}%");
        $queryParams = $req->query();
        $queryParams = Arr::except($queryParams, ['q']);
        foreach ($queryParams as $key => $value) {
            $query->where($key, $value);
        }

        $data = $query->get();

        return response()->json($data->map(function($expense) {
            return [
                'id'   => $expense->expense_id,
                'text' => $expense->id_expense,
                'data' => $expense,
            ];
        }));
    }
}
