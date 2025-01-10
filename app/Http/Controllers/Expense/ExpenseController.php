<?php

namespace App\Http\Controllers\Expense;

use App\Constants;
use App\Helpers\Parser;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Location;
use App\Models\Expense\Expense;
use App\Models\Expense\ExpenseAdditPrice;
use App\Models\Expense\ExpenseKandang;
use App\Models\Expense\ExpenseMainPrice;
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

            $data = Expense::with(['location', 'expense_payments', 'created_user'])
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
            $input = $req->query();
            $data  = null;

            if (count($input) > 0) {
                $query = Expense::with([
                    'expense_main_prices',
                    'expense_addit_prices',
                    'expense_kandang.kandang',
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

                $expenses = $query->get();

                $data = $expenses->map(function($expense) {
                    return [
                        'expense_id' => $expense->expense_id,
                        'created_at' => $expense->created_at,
                        'location'   => $expense->location->name,
                        'category'   => $expense->category, // 1 : BOP, 2 : Non-BOP
                        'farms'      => $expense->category == 2
                            ? []
                            : $expense->expense_kandang->map(function($e_kandang) {
                                return $e_kandang->kandang->name;
                            }),
                        'main_prices' => $expense->expense_main_prices
                            ->map(function($mp) {
                                return [
                                    'name'  => $mp->sub_category,
                                    'qty'   => $mp->qty,
                                    'uom'   => $mp->uom,
                                    'price' => $mp->price,
                                ];
                            }),
                        'addit_prices' => $expense->expense_addit_prices
                            ->map(function($ap) {
                                return [
                                    'name'  => $ap->name,
                                    'price' => $ap->price,
                                ];
                            }),
                    ];
                });

            }

            $old                  = $req->query();
            $old['location_id']   = $location->location_id ?? null;
            $old['location_name'] = $location->name        ?? '';

            $param = [
                'title' => 'Biaya > List',
                'data'  => $data,
                'old'   => $old,
            ];

            return view('expense.recap.index', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function exportRecap(Request $req)
    {
        //
    }

    public function recapExport()
    {
        try {
            //
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

                DB::transaction(function() use ($req) {
                    $input         = $req->all();
                    $category      = $input['category'];
                    $expenseStatus = $input['expense_status'];
                    $expenseID     = 0;

                    if ($expenseStatus == 0) {
                        $createdExpense = Expense::create([
                            'location_id'    => $input['location_id'],
                            'category'       => $category,
                            'payment_status' => 0,
                            'expense_status' => 0,
                            'created_by'     => Auth::id(),
                        ]);

                        $expenseID = $createdExpense->expense_id;
                    } else {
                        $createdExpense = Expense::create([
                            'location_id'    => $input['location_id'],
                            'category'       => $category,
                            'payment_status' => 1,
                            'expense_status' => 1,
                            'created_by'     => Auth::id(),
                        ]);

                        $expenseID = $createdExpense->expense_id;
                    }

                    $selectedKandangs = json_decode($req->input('selected_kandangs'), true);
                    if (count($selectedKandangs) > 0) {
                        $create = false;

                        $arrKandang = [];
                        foreach ($selectedKandangs as $key => $value) {
                            if ($category == 1) {
                                $create                         = true;
                                $arrKandang[$key]['expense_id'] = $expenseID;
                                $arrKandang[$key]['kandang_id'] = $value;
                            }
                        }
                        ExpenseKandang::insert($arrKandang);
                    }

                    if ($req->has('expense_main_prices')) {
                        $arrMainPrices = $req->input('expense_main_prices');

                        foreach ($arrMainPrices as $key => $value) {
                            $qty        = Parser::parseLocale($value['qty']);
                            $totalPrice = Parser::parseLocale($value['price']);

                            $arrMainPrices[$key]['expense_id']   = $expenseID;
                            $arrMainPrices[$key]['sub_category'] = $value['sub_category'];
                            $arrMainPrices[$key]['qty']          = $qty;
                            $arrMainPrices[$key]['uom']          = $value['uom'];
                            $arrMainPrices[$key]['price']        = $totalPrice;
                            $arrMainPrices[$key]['notes']        = $value['notes'];
                        }

                        ExpenseMainPrice::insert($arrMainPrices);
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
                        }
                        if ($create) {
                            ExpenseAdditPrice::insert($arrAdditPrices);
                        }
                    }

                    if ($expenseStatus == 1) {
                        $prefix      = $category == 1 ? 'OP' : 'NB';
                        $incrementId = Expense::where('id_expense', 'LIKE', "{$prefix}.%")->withTrashed()->count() + 1;
                        $idExpense   = "{$prefix}.{$incrementId}";

                        $createdExpense->update([
                            'id_expense' => $idExpense,
                        ]);
                    }
                });

                $success = ['success' => 'Data Berhasil disimpan'];

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

    public function detail(Expense $expense)
    {
        try {
            $data = $expense->load([
                'created_user',
                'location',
                'expense_kandang.kandang',
                'expense_main_prices',
                'expense_addit_prices',
                'expense_payments',
            ]);

            $param = [
                'title' => 'Biaya > Detail',
                'data'  => $data,
            ];

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

                DB::transaction(function() use ($req, $expense) {
                    $input = $req->all();

                    if ($expense->expense_status == 1) {
                        $expense->update([
                            'location_id'    => $input['location_id'],
                            'payment_status' => 1,
                            'expense_status' => 1,
                        ]);
                    }

                    $expense->expense_kandang()->delete();
                    $selectedKandangs = json_decode($req->input('selected_kandangs'), true);
                    if (count($selectedKandangs) > 0) {
                        $create = false;

                        $arrKandang = [];
                        foreach ($selectedKandangs as $key => $value) {
                            if ($expense->category == 1) {
                                $create                         = true;
                                $arrKandang[$key]['expense_id'] = $expense->expense_id;
                                $arrKandang[$key]['kandang_id'] = $value;
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

                            $arrMainPrices[$key]['expense_id']   = $expense->expense_id;
                            $arrMainPrices[$key]['sub_category'] = $value['sub_category'];
                            $arrMainPrices[$key]['qty']          = $qty;
                            $arrMainPrices[$key]['uom']          = $value['uom'];
                            $arrMainPrices[$key]['price']        = $totalPrice;
                            $arrMainPrices[$key]['notes']        = $value['notes'];
                        }

                        ExpenseMainPrice::insert($arrMainPrices);
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
                        }
                        if ($create) {
                            ExpenseAdditPrice::insert($arrAdditPrices);
                        }
                    }

                    $prefix      = $expense->category == 1 ? 'OP' : 'NB';
                    $incrementId = Expense::where('id_expense', 'LIKE', "{$prefix}.%")->withTrashed()->count() + 1;
                    $idExpense   = "{$prefix}.{$incrementId}";

                    $expense->update([
                        'id_expense' => $idExpense,
                    ]);
                });

                $success = ['success' => 'Data Berhasil diubah'];

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

    public function delete(Expense $expense)
    {
        try {
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

    public function approve(Request $req, Expense $expense)
    {
        DB::beginTransaction();
        try {
            $input = $req->all();

            $success       = ['success' => 'Biaya berhasil ditolak'];
            $approvedAt    = null;
            $expenseStatus = array_search('Ditolak', Constants::EXPENSE_STATUS);

            if ($input['is_approved'] == 1) {
                $success       = ['success' => 'Biaya berhasil disetujui'];
                $approvedAt    = date('Y-m-d H:i:s');
                $expenseStatus = array_search('Disetujui', Constants::EXPENSE_STATUS);
            }

            $expense->update([
                'is_approved'    => $input['is_approved'],
                'approver_id'    => Auth::id(),
                'approval_notes' => $input['approval_notes'],
                'approved_at'    => $approvedAt,
                'expense_status' => $expenseStatus,
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
        $query  = Expense::with(['location', 'created_user', 'expense_payments'])
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
