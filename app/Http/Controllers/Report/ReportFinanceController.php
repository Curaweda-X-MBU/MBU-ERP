<?php

namespace App\Http\Controllers\Report;

use App\Constants;
use App\Helpers\Parser;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportFinanceController extends Controller
{
    public function customerPayment(Request $req)
    {
        try {
            $data = Customer::with(['marketings', 'marketings.sales', 'marketings.marketing_products'])
                ->whereHas('marketings', function($q) {
                    $q->where('marketing_status', '>', 2);
                });

            $request   = $req->all();
            $rows      = $req->has('rows') ? $req->get('rows') : 10;
            $arrAppend = [
                'rows' => $rows,
                'page' => 1,
            ];

            foreach ($request as $key => $value) {
                if ($value !== '-all' && ! in_array($key, ['rows', 'page'])) {
                    if (is_array($value)) {
                        $data->whereHas($key, function($query) use ($value) {
                            $this->applyNestedWhere($query, $value, $arrAppend);
                        });
                    } else {
                        $data->where($key, $value);
                        $arrAppend[$key] = $value;
                    }
                }
            }

            $data = $data
                ->orderBy('customer_id', 'DESC')
                ->paginate($rows);
            $data->appends($arrAppend);

            $paginated = $data->mapWithKeys(function($c) {
                $products = $c->marketings->flatMap(function($m) {
                    return $m->marketing_products->map(function($mp) use ($m) {
                        return [
                            'tanggal'     => Carbon::parse($m->created_at)->format('d-M-Y'),
                            'referensi'   => $m->id_marketing,
                            'nopol'       => '',
                            'qty'         => Parser::trimLocale($mp->qty),
                            'berat'       => 0,
                            'avg'         => 0,
                            'harga'       => Parser::toLocale($mp->price),
                            'diskon'      => Parser::toLocale($m->discount),
                            'tabungan'    => 0,
                            'pajak'       => $m->tax,
                            'total'       => Parser::toLocale($m->grand_total),
                            'pembayaran'  => Parser::toLocale($m->is_paid),
                            'saldo'       => Parser::toLocale($m->not_paid),
                            'keterangan'  => Constants::MARKETING_PAYMENT_STATUS[$m->payment_status],
                            'Pengambilan' => $mp->warehouse->name,
                            'sales'       => optional($m->sales)->name,
                        ];
                    });
                });

                return [
                    $c->name => $products,
                ];
            });

            $param = [
                'title'     => 'Laporan > Pembayaran Customer',
                'data'      => $data,
                'paginated' => $paginated,
            ];

            return view('report.finance.customer-payment', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    private function applyNestedWhere($query, $values, &$arrAppend)
    {
        foreach ($values as $relationKey => $relationValue) {
            if (is_array($relationValue)) {
                $query->whereHas($relationKey, function($subQuery) use ($relationValue) {
                    $this->applyNestedWhere($subQuery, $relationValue, $arrAppend);
                });
            } else {
                $query->where($relationKey, $relationValue);
                $arrAppend[$relationKey] = $relationValue;
            }
        }
    }

    public function balanceMonitoring()
    {
        try {
            $param = [
                'title' => 'Laporan > Monitoring Saldo',
            ];

            return view('report.finance.balance-monitoring', $param);
        } catch (\Exception $e) {
            // throw $th;
        }
    }
}
