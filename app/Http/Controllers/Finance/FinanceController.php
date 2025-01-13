<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\DataMaster\Location;
use App\Models\Expense\Expense;
use App\Models\Marketing\Marketing;
use App\Models\Project\Project;
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
            $input = $req->query();
            $data  = collect();

            if (count($input) > 0) {
                $location = Location::find($input['location_id']);

                $project = Project::where('project_id', $location->location_id);

                // EXPENSE by location_id
                $expenses = Expense::where('location_id', $location->location_id);

                // MARKETING by company_id
                $marketings = Marketing::whereHas('marketing_payments')->with(['marketing_payments', 'marketing_products'])->where('company_id', $input['company_id'])->get()
                    ->flatMap(function($m) use ($input) {
                        $m->marketing_products->where('warehouse.kandang_id', $input['kandang_id'])
                            ->map(function($p) {
                                $p->id_marketing     = $p->marketing->id_marketing;
                                $p->transaction_type = 'Penjualan';
                                $p->created_by       = $p->marketing->created_user->name;
                                $p->created_at       = $p->marketing->created_at;
                                $p->payment_at       = $p->marketing->marketing_payments->where('approved_at', '!=', 'null')->first()->payment_at;
                                $p->payment_status   = $p->marketing->payment_status;
                            });
                    });

                dd($marketings);

                // PURCHASE by company_id
                //$purchases = Purchase::whereHas('purchase_item');
            }

            $param = [
                'title' => 'Keuangan > List',
                'data'  => $data,
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
