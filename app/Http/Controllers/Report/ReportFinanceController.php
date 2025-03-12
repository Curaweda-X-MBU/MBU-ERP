<?php

namespace App\Http\Controllers\Report;

use App\Constants;
use App\Helpers\Parser;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Customer;
use App\Models\Marketing\Marketing;
use App\Models\Marketing\MarketingProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportFinanceController extends Controller
{
    public function customerPayment(Request $req)
    {
        try {
            $request   = $req->all();
            $rows      = $req->has('rows') ? $req->get('rows') : 10;
            $arrAppend = [
                'rows' => $rows,
                'page' => 1,
            ];

            // STEP 1
            $customers = Customer::query()->whereHas('marketings');
            if ($req->has('customer_id') && $req->get('customer_id')) {
                $customers->where('customer_id', $req->get('customer_id'));
            }
            $customers = $customers->orderBy('customer_id', 'DESC')
                ->paginate($rows);
            $customers->appends($arrAppend);

            $customerIds = $customers->pluck('customer_id');

            // STEP 2
            $marketingProducts = MarketingProduct::query()
                ->whereHas('marketing', function($q) use ($customerIds, $request) {
                    $q->whereIn('customer_id', $customerIds);

                    foreach ($request as $key => $value) {
                        if (is_array($value) && $key === 'marketings') {
                            $this->applyNestedWhere($q, $value, $arrAppend);
                        }
                    }
                })
                ->with(['marketing.sales', 'warehouse'])
                ->get()
                ->groupBy('marketing.customer_id');

            $paginated = $customers->mapWithKeys(function($c) use ($marketingProducts) {
                $products = $marketingProducts->get($c->customer_id, collect())->map(function($mp) {
                    $m = $mp->marketing;

                    return (object) [
                        'tanggal'     => Carbon::parse($m->created_at)->format('d-M-Y'),
                        'referensi'   => $m->id_marketing,
                        'nopol'       => '???',
                        'qty'         => Parser::trimLocale($mp->qty),
                        'berat'       => Parser::trimLocale($mp->weight_total),
                        'avg'         => Parser::trimLocale($mp->weight_avg),
                        'harga'       => Parser::toLocale($mp->price),
                        'diskon'      => Parser::toLocale($mp->local_discount),
                        'tabungan'    => '???',
                        'pajak'       => $m->tax ?? 0,
                        'total'       => Parser::toLocale($mp->grand_total),
                        'pembayaran'  => Parser::toLocale($mp->is_paid),
                        'saldo'       => Parser::toLocale($mp->grand_total - $mp->is_paid),
                        'keterangan'  => Constants::MARKETING_PAYMENT_STATUS[$m->payment_status],
                        'pengambilan' => $mp->warehouse->name,
                        'sales'       => optional($m->sales)->name,
                    ];
                });

                return [
                    $c->name => (object) [
                        'customer' => (object) [
                            'name'    => $c->name,
                            'address' => $c->address,
                            'nik'     => '???',
                            'npwp'    => $c->tax_num,
                        ],
                        'products' => $products,
                    ],
                ];
            });

            $param = [
                'title'     => 'Laporan > Pembayaran Customer',
                'data'      => $customers,
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

    public function balanceMonitoring(Request $req)
    {
        try {
            $request   = $req->all();
            $rows      = $req->has('rows') ? $req->get('rows') : 10;
            $arrAppend = [
                'rows' => $rows,
                'page' => 1,
            ];

            $customers = Customer::query()
                ->whereHas('marketings', fn ($q) => $q->where('payment_status', '<', array_search('Dibayar Penuh', Constants::MARKETING_PAYMENT_STATUS)));
            if ($req->has('customer_id') && $req->get('customer_id')) {
                $customers->where('customer_id', $req->get('customer_id'));
            }
            $customers = $customers->orderBy('customer_id', 'DESC')
                ->paginate($rows);
            $customers->appends($arrAppend);

            $customerIds = $customers->pluck('customer_id');

            // STEP 2
            $marketings = Marketing::query()
                ->whereIn('customer_id', $customerIds)
                ->with(['marketing_products', 'marketing_payments']);

            foreach ($request as $key => $value) {
                if (is_array($value) && $key === 'marketings') {
                    $this->applyNestedWhere($marketings, $value, $arrAppend);
                }
            }

            $marketings = $marketings
                ->get()
                ->groupBy('customer_id');

            $paginated = $customers->transform(function($c) use ($marketings) {
                $cm = $marketings->get($c->customer_id, collect());

                $aging = $cm->pluck('realized_at')->map(function($r) {
                    return (int) Carbon::parse($r)->diffInDays(now());
                });
                $ayamEkor    = $cm->flatMap->marketing_products->sum('qty');
                $ayamKg      = $cm->flatMap->marketing_products->sum('weight_total');
                $ayamNominal = $cm->sum('grand_total');
                $pembayaran  = $cm->sum('is_paid');
                $hutang      = $cm->sum('not_paid');

                return (object) [
                    'customer'     => $c->name,
                    'saldo_awal'   => '???',
                    'ayam_ekor'    => Parser::trimLocale($ayamEkor),
                    'ayam_kg'      => Parser::trimLocale($ayamKg),
                    'ayam_nominal' => Parser::toLocale($ayamNominal),
                    'trading'      => '???',
                    'pembayaran'   => Parser::toLocale($pembayaran),
                    'hutang'       => Parser::toLocale($hutang),
                    'aging'        => Parser::trimLocale($aging->sum()),
                    'aging_avg'    => Parser::trimLocale($aging->avg()),
                    'saldo_akhir'  => '???',
                    // Raw values for calculating
                    'raw_saldo_awal'   => 0,
                    'raw_ayam_ekor'    => $ayamEkor,
                    'raw_ayam_kg'      => $ayamKg,
                    'raw_ayam_nominal' => $ayamNominal,
                    'raw_trading'      => 0,
                    'raw_pembayaran'   => $pembayaran,
                    'raw_hutang'       => $hutang,
                    'raw_aging'        => $aging->sum(),
                    'raw_aging_avg'    => $aging->avg(),
                    'raw_saldo_akhir'  => $hutang,
                ];
            });

            $param = [
                'title'     => 'Laporan > Monitoring Saldo',
                'data'      => $customers,
                'paginated' => $paginated,
            ];

            return view('report.finance.balance-monitoring', $param);
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
                if (isset($relationValue['start']) || isset($relationValue['end'])) {
                    $start = Carbon::parse($relationValue['start'] ?? '1970-01-01')->startOfDay();
                    $end   = Carbon::parse($relationValue['end'] ?? now())->endOfDay();

                    $query->whereBetween($relationKey, [$start, $end]);
                    $arrAppend[$relationKey] = $relationValue;
                } else {
                    if ($relationKey !== 'created_at') {
                        $query->whereHas($relationKey, function($subQuery) use ($relationValue, &$arrAppend) {
                            $this->applyNestedWhere($subQuery, $relationValue, $arrAppend);
                        });
                    }
                }
            } else {
                $query->where($relationKey, $relationValue);
                $arrAppend[$relationKey] = $relationValue;
            }
        }
    }
}
