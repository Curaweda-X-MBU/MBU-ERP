<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Kandang;
use App\Models\DataMaster\Location;
use App\Models\Expense\Expense;
use App\Models\Marketing\Marketing;
use App\Models\Purchase\Purchase;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {
        try {
            $input      = $req->query();
            $data       = collect();
            $expenses   = collect();
            $marketings = collect();

            if (count($input) > 0) {
                $location = Location::find($input['location_id']);
                $company  = Company::find($input['company_id']);
                $kandang  = Kandang::find($input['kandang_id']);

                // EXPENSE by location_id
                $expenses = Expense::whereHas('expense_payments')->where('location_id', $input['location_id'])->get()
                    ->flatMap(function($e) {
                        $expense_items = $e->expense_main_prices->map(function($mp) {
                            $mp->id_item          = $mp->expense->id_expense;
                            $mp->transaction_type = 'Biaya';
                            $mp->created_by       = $mp->expense->created_user->name;
                            $mp->created_at       = $mp->expense->created_at;
                            $mp->payment_at       = $mp->expense->expense_payments->where('approved_at', '!=', 'null')->first()->payment_at;
                            $mp->payment_status   = $mp->expense->payment_status;
                            $mp->name             = $mp->sub_category;
                            $mp->pengeluaran      = $mp->total_price;

                            return $mp;
                        });

                        $expense_items = $expense_items->concat($e->expense_addit_prices->map(function($ap) {
                            $ap->id_item          = $ap->expense->id_expense;
                            $ap->transaction_type = 'Biaya';
                            $ap->created_by       = $ap->expense->created_user->name;
                            $ap->created_at       = $ap->expense->created_at;
                            $ap->payment_at       = $ap->expense->expense_payments->where('approved_at', '!=', 'null')->first()->payment_at;
                            $ap->payment_status   = $ap->expense->payment_status;
                            $ap->pengeluaran      = $ap->total_price;

                            return $ap;
                        }));

                        return $expense_items;
                    });

                // MARKETING by company_id
                $marketings = Marketing::whereHas('marketing_payments')->where('company_id', $input['company_id'])->with(['marketing_payments', 'marketing_products'])->get()
                    ->flatMap(function($m) use ($input) {
                        return $m->marketing_products->where('warehouse.kandang_id', $input['kandang_id'])
                            ->map(function($p) {
                                $p->id_item          = $p->marketing->id_marketing;
                                $p->transaction_type = 'Penjualan';
                                $p->created_by       = $p->marketing->created_user->name;
                                $p->created_at       = $p->marketing->created_at;
                                $p->payment_at       = $p->marketing->marketing_payments->where('approved_at', '!=', 'null')->first()->payment_at;
                                $p->payment_status   = $p->marketing->payment_status;
                                $p->name             = $p->product->name;
                                $p->pemasukan        = $p->total_price;

                                return $p;
                            });
                    });

                // PURCHASE by company_id
                // $purchases = Purchase::whereHas('purchase_item');
            }

            $data = $data->concat($expenses)->concat($marketings);

            if (isset($input['date_start'])) {
                $data = $data->whereBetween('created_at', [
                    $input['date_start'], $input['date_end'] ?? now(),
                ]);
            }

            $old                  = $req->query();
            $old['location_id']   = isset($location) ? $location->location_id : null;
            $old['location_name'] = isset($location) ? $location->name : null;
            $old['company_id']    = isset($company) ? $company->company_id : null;
            $old['company_name']  = isset($company) ? $company->name : null;
            $old['kandang_id']    = isset($kandang) ? $kandang->kandang_id : null;
            $old['kandang_name']  = isset($kandang) ? $kandang->name : null;
            $param                = [
                'title' => 'Keuangan > List',
                'data'  => $data,
                'old'   => $old,
            ];

            return view('finance.index', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
}
