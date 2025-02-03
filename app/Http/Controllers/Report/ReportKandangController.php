<?php

namespace App\Http\Controllers\Report;

use App\Constants;
use App\Helpers\Parser;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Location;
use App\Models\Expense\Expense;
use App\Models\Inventory\StockMovement;
use App\Models\Marketing\Marketing;
use App\Models\Project\Project;
use App\Models\Project\Recording;
use App\Models\Purchase\Purchase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportKandangController extends Controller
{
    private function checkAccess($company, $param, $view, $permission = null)
    {
        try {
            if (empty($permission)) {
                $permission = 'report.'.strtolower($company->alias).'.'.$view;
            }

            $roleAccess = Auth::user()->role;
            switch ($company->alias) {
                case 'MBU':
                    if ($roleAccess->hasPermissionTo($permission)) {
                        return view("report.mbu.{$view}", $param);
                    }
                case 'LTI':
                    if ($roleAccess->hasPermissionTo($permission)) {
                        return view("report.lti.{$view}", $param);
                    }
                case 'MAN':
                    if ($roleAccess->hasPermissionTo($permission)) {
                        return view("report.man.{$view}", $param);
                    }
                default:
                    throw new \Exception('Invalid company');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function detail(Request $req, Location $location, Project $project)
    {
        try {
            $company = Company::where('alias', strtoupper($req->query('company')))
                ->select(['company_id', 'name', 'alias'])
                ->first();

            if (empty($company)) {
                throw new \Exception('Invalid company');
            }

            $detail = (object) [
                'location_id'    => $location->location_id,
                'project_id'     => $project->project_id,
                'location'       => $location->name,
                'period'         => $project->period,
                'product'        => $project->product_category->name,
                'doc'            => $project->project_chick_in->first()->total_chickin ?? 0,
                'farm_type'      => $project->farm_type,
                'closing_date'   => Carbon::parse($project->closing_date)->format('d-M-Y') ?? '-',
                'project_status' => $project->project_status,
                'kandang_name'   => $project->kandang->name,
                'chickin_date'   => $project->project_chick_in->first()->chickin_date ?? null,
                'ppl_ts'         => $project->kandang->user->name,
                'approval_date'  => $project->approval_date,
                'data_produksi'  => $this->dataProduksi(app(Request::class), $location, $project),
            ];

            $param = [
                'title'  => 'Laporan > MBU',
                'detail' => $detail,
            ];

            return $this->checkAccess($company, $param, 'kandang');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function sapronak(Request $req, Location $location, Project $project)
    {
        try {
            $period = intval($req->query('period') ?? $project->period);

            $sapronakMasuk  = $this->getSapronakMasuk($period, $location, $project);
            $sapronakKeluar = $this->getSapronakKeluar($period, $location, $project);

            $sapronak = [
                'sapronak_masuk'  => $sapronakMasuk,
                'sapronak_keluar' => $sapronakKeluar,
            ];

            return response()->json($sapronak);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // * ON PROGRESS
    public function perhitunganSapronak(Request $req, Location $location, Project $project)
    {
        try {
            $period = intval($req->query('period') ?? $project->period);

            $recordingItems = Recording::with([
                'project',
                'recording_depletion.product_warehouse.product',
            ])
                ->whereHas('project', function($query) use ($project, $period) {
                    $query->where([
                        ['project_id', $project->project_id],
                        ['period', $period],
                    ]);
                })
                ->get()
                ->flatMap(function($recording) {
                    return $recording->recording_depletion->map(fn ($item) => [
                        'produk' => optional($item->product_warehouse->product)->name ?? '-',
                        'qty'    => Parser::toLocale($item->total ?? $item->decrease).' '.optional($item->product_warehouse->product->uom)->name,
                    ]);
                });

            // $purchaseItems = Purchase::with(['warehouse.kandang.project', 'purchase_item', 'purchase_item.product'])
            //     ->whereHas('warehouse.kandang.project', function($query) use ($project, $period) {
            //         $query->where([
            //             ['project_id', $project->project_id],
            //             ['period', $period]
            //         ]);
            //     })
            //     ->get()
            //     ->flatMap(function($p) {
            //         return $p->purchase_item->map(function($item) use ($p) {
            //             return [
            //                 'qty_masuk' => $item->qty,
            //                 'produk' => $item->product->name,
            //                 'harga_satuan' => $item->price,
            //                 'total_harga' => $item->total,
            //                 'notes' => $p->notes
            //             ];
            //         });
            //     });

            return [
                'doc' => $recordingItems,
            ];

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function penjualan(Request $req, Location $location, Project $project)
    {
        try {
            $period = intval($req->query('period') ?? $project->period);

            $marketings = Marketing::with([
                'marketing_products.product.uom',
                'marketing_products.warehouse.kandang',
                'marketing_addit_prices',
                'customer',
                'marketing_products.project.project_chick_in' => function($ci) {
                    $ci->orderBy('chickin_date');
                },
            ])
                ->where([
                    ['marketing_status', '>=', 3],
                    ['marketing_return_id', '=', null],
                ])
                ->whereHas(
                    'marketing_products.project',
                    fn ($query) => $query->where([
                        ['project_id', $project->project_id],
                        ['period', $period],
                    ])
                )
                ->whereHas(
                    'marketing_products.warehouse.kandang',
                    fn ($query) => $query->where('location_id', $location->location_id)
                )
                ->get()
                ->transform(function($m) use ($project) {
                    $tanggal     = $m->realized_at ? Carbon::parse($m->realized_at)->format('d-M-Y') : '-';
                    $chickinDate = Carbon::parse($project->project_chick_in->first()->chickin_date);
                    $umur        = $m->realized_at ? $chickinDate->diffInDays(Carbon::parse($m->realized_at)) : '-';

                    return [
                        'tanggal'                => $tanggal,
                        'umur'                   => $umur,
                        'no_do'                  => $m->id_marketing,
                        'customer'               => $m->customer->name,
                        'jumlah_ekor'            => $m->marketing_products->sum('qty'),
                        'jumlah_kg'              => $m->marketing_products->sum('weight_total'),
                        'harga'                  => Parser::toLocale($m->marketing_products->sum('price')),
                        'cn'                     => Parser::toLocale($m->not_paid),
                        'total'                  => Parser::toLocale($m->grand_total),
                        'kandang'                => $project->kandang->name,
                        'status'                 => [$m->payment_status, Constants::MARKETING_PAYMENT_STATUS[$m->payment_status]],
                        'marketing_products'     => $m->marketing_products,
                        'marketing_addit_prices' => $m->marketing_addit_prices,
                    ];
                });

            return response()->json($marketings);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function overhead(Request $req, Location $location, Project $project)
    {
        try {
            $period = intval($req->query('period') ?? $project->period);

            $expense = Expense::with('expense_kandang', 'expense_kandang.project', 'expense_main_prices', 'expense_addit_prices')
                ->whereHas('expense_kandang.project', function($query) use ($project, $period) {
                    $query->where([
                        ['project_id', $project->project_id],
                        ['period', $period],
                    ]);
                })
                ->where([
                    ['location_id', $location->location_id],
                    ['expense_status', 2],
                ])
                ->get()
                ->flatMap(function($e) {
                    return $e->expense_main_prices->map(function($mp) {
                        return [
                            'tanggal' => Carbon::parse($mp->approved_at)->format('d-M-Y'),
                            'produk'  => "{$mp->sub_category} (BOP)",
                            'qty'     => "{$mp->qty} {$mp->uom}",
                            'price'   => Parser::toLocale($mp->price / $mp->qty),
                            'total'   => Parser::toLocale($mp->price),
                        ];
                    })->concat($e->expense_addit_prices->map(function($ap) {
                        return [
                            'tanggal' => Carbon::parse($ap->approved_at)->format('d-M-Y'),
                            'produk'  => "{$ap->name} (NBOP)",
                            'qty'     => '-',
                            'price'   => Parser::toLocale($ap->price),
                            'total'   => Parser::toLocale($ap->price),
                        ];
                    }));
                });

            return response()->json($expense);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function hppEkspedisi(Request $req, Location $location, Project $project)
    {
        try {
            $period = intval($req->query('period') ?? $project->period);

            $marketingDeliveries = Marketing::with(['marketing_products', 'marketing_products.warehouse.kandang', 'marketing_delivery_vehicles.supplier'])
                ->whereHas('marketing_products.project', function($query) use ($project, $period) {
                    $query->where([
                        ['project_id', $project->project_id],
                        ['period', $period],
                    ]);
                })
                ->whereHas('marketing_products.warehouse.kandang', function($query) use ($location) {
                    $query->where('location_id', $location->location_id);
                })->get()
                ->flatMap(function($m) {
                    return $m->marketing_delivery_vehicles->map(function($d) {
                        return [
                            'id_marketing'       => $d->marketing->id_marketing,
                            'supplier_name'      => $d->supplier->name,
                            'total_delivery_fee' => $d->delivery_fee,
                        ];
                    });
                });

            return response()->json($marketingDeliveries);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function dataProduksi(Request $req, Location $location, Project $project)
    {
        try {
            $period = intval($req->query('period') ?? $project->period);

            $pembelian   = $this->getProduksiPembelian($period, $location, $project);
            $penjualan   = $this->getProduksiPenjualan($period, $location, $project);
            $performence = $this->getProduksiPerformence($period, $location, $project);

            return response()->json([
                'pembelian'   => $pembelian,
                'penjualan'   => $penjualan,
                'performence' => $performence,
                'selisih'     => null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // TODO
    public function keuangan(Request $req, Location $location, Project $project)
    {
        try {
            // code...
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getSapronakMasuk(int $period, Location $location, Project $project)
    {
        $sapronakMasuk = StockMovement::with(['product', 'product.uom', 'origin', 'destination', 'destination.kandang.project'])
            ->whereHas('destination.kandang.project', function($query) use ($project, $period) {
                $query->where([
                    ['project_id', $project->project_id],
                    ['period', $period],
                ]);
            })
            ->whereHas('destination', function($query) use ($location) {
                $query->where('location_id', $location->location_id);
            })
            ->get()
            ->transform(function($si) {
                return [
                    'tanggal'      => Carbon::parse($si->created_at)->format('d-M-Y'),
                    'no_referensi' => $si->stock_movement_id,
                    'transaksi'    => 'Mutasi Masuk',
                    'produk'       => $si->product->name,
                    'gudang_asal'  => $si->origin->name,
                    'qty'          => Parser::toLocale($si->transfer_qty).' '.$si->product->uom->name,
                    'notes'        => $si->notes,
                ];
            });

        $purchaseItems = Purchase::with(['warehouse.kandang.project', 'purchase_item', 'purchase_item.product'])
            ->whereHas('warehouse.kandang.project', function($query) use ($project, $period) {
                $query->where([
                    ['project_id', $project->project_id],
                    ['period', $period],
                ]);
            })
            ->get()
            ->flatMap(function($p) {
                return $p->purchase_item->map(function($item) use ($p) {
                    return [
                        'tanggal'      => Carbon::parse(json_decode($p->approval_line)[4]->date)->format('d-M-Y'),
                        'no_referensi' => $p->po_number ?? '-',
                        'transaksi'    => 'Pembelian',
                        'produk'       => $item->product->name,
                        'gudang_asal'  => '-',
                        'qty'          => Parser::toLocale($item->qty).' '.$item->product->uom->name,
                        'notes'        => $p->notes,
                        'harga_satuan' => $item->price,
                    ];
                });
            });

        return $sapronakMasuk->concat($purchaseItems);
    }

    private function getSapronakKeluar(int $period, Location $location, Project $project)
    {
        $sapronakKeluar = StockMovement::with([
            'product:product_id,name,uom_id',
            'product.uom:uom_id,name',
            'origin:warehouse_id,name',
            'destination:warehouse_id,name',
            'origin.kandang.project:project_id,period',
        ])
            ->select(['stock_movement_id', 'product_id', 'origin_id', 'destination_id', 'created_at', 'transfer_qty', 'notes'])
            ->whereHas(
                'origin.kandang.project',
                fn ($q) => $q->where([['project_id', $project->project_id], ['period', $period]])
            )
            ->whereHas(
                'origin',
                fn ($q) => $q->where('location_id', $location->location_id)
            )
            ->get()
            ->transform(fn ($so) => [
                'tanggal'       => Carbon::parse($so->created_at)->format('d-M-Y'),
                'no_referensi'  => $so->stock_movement_id,
                'transaksi'     => 'Mutasi Keluar',
                'produk'        => $so->product->name,
                'gudang_tujuan' => optional($so->destination)->name,
                'qty'           => Parser::toLocale($so->transfer_qty).' '.optional($so->product->uom)->name,
                'notes'         => $so->notes,
            ]);

        $recordingItems = Recording::with([
            'recording_stock.product_warehouse.product',
            'recording_depletion.product_warehouse.product',
            'recording_egg.product_warehouse.product',
            'project:project_id,period',
        ])
            ->whereHas('project', fn ($p) => $p->where([['project_id', $project->project_id], ['period', $period]]))
            ->get()
            ->flatMap(fn ($recording) => $this->formatRecordingItems($recording));

        return $sapronakKeluar->concat($recordingItems);
    }

    private function formatRecordingItems($recording)
    {
        $recordingData = collect([
            'recording_stock'     => 'Persediaan',
            'recording_depletion' => 'Deplesi',
            'recording_egg'       => 'Telur',
        ]);

        return $recordingData->flatMap(function($type, $relation) use ($recording) {
            return $recording->$relation->map(fn ($item) => [
                'tanggal'       => Carbon::parse($recording->created_at)->format('d-M-Y H:i:s'),
                'no_referensi'  => $recording->recording_id,
                'transaksi'     => 'Recording '."({$type})",
                'produk'        => optional($item->product_warehouse->product)->name ?? '-',
                'gudang_tujuan' => '-',
                'qty'           => Parser::toLocale($item->total ?? $item->decrease).' '.optional($item->product_warehouse->product->uom)->name,
                'notes'         => $item->notes,
            ]);
        });
    }

    private function getProduksiPenjualan(int $period, Location $location, Project $project)
    {
        $getMarketings = Marketing::with([
            'marketing_products.warehouse.kandang',
            'marketing_products.product',
            'marketing_products.project.project_chick_in',
            'marketing_products.uom',
            'marketing_addit_prices',
            'customer',
        ])
            ->whereNull('marketing_return_id')
            ->where('marketing_status', '>=', 3)
            ->whereHas(
                'marketing_products.project',
                fn ($query) => $query->where([
                    ['project_id', $project->project_id],
                    ['period', $period],
                ])
            )
            ->whereHas(
                'marketing_products.warehouse.kandang',
                fn ($query) => $query->where('location_id', $location->location_id)
            )
            ->get();

        $firstProduct = $getMarketings->pluck('marketing_products')->flatten()->first();
        $marketingUom = optional(optional($firstProduct)->uom)->name ?? 'Unit';

        $totalWeightKg = $getMarketings->sum(fn ($m) => $m->marketing_products->sum('weight_total'));
        $totalUnit     = $getMarketings->sum(fn ($m) => $m->marketing_products->sum('qty'));
        $totalPrice    = $getMarketings->sum(fn ($m) => $m->marketing_products->sum('total_price'));

        $averagePricePerKg    = $totalWeightKg > 0 ? $totalPrice    / $totalWeightKg : 0;
        $averageWeightPerUnit = $totalUnit     > 0 ? $totalWeightKg / $totalUnit : 0;

        return [
            'penjualan_kg'    => Parser::toLocale($totalWeightKg).' Kg',
            'penjualan_ekor'  => Parser::toLocale($totalUnit)." {$marketingUom}",
            'bobot_rata'      => Parser::toLocale($averageWeightPerUnit)." Kg/{$marketingUom}",
            'harga_jual_rata' => Parser::toLocale($averagePricePerKg),
            'total_harga'     => Parser::toLocale($totalPrice),
        ];
    }

    private function getProduksiPembelian(int $period, Location $location, Project $project)
    {
        $pembelian = $project->with([
            'recording' => function($query) {
                $query->whereHas('recording_depletion.product_warehouse.product', function($q) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%culling%']);
                })->withSum(['recording_depletion as culling_total' => function($q) {
                    $q->whereHas('product_warehouse.product', function($q) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%culling%']);
                    });
                }], 'total');
            },
            'project_chick_in',
        ])
            ->where([
                ['project_id', $project->project_id],
                ['period', $period],
            ])
            ->get()
            ->flatMap(function($p) {
                $populasiAwal = optional($p->project_chick_in->first())->total_chickin ?? 0;
                $culling      = $p->recording->sum('culling_total')                    ?? 0;

                return [
                    'populasi_awal'       => $populasiAwal,
                    'culling'             => $culling,
                    'populasi_akhir'      => $populasiAwal - $culling,
                    'pakan_masuk'         => 0,
                    'pakan_terpakai'      => 0,
                    'pakan_terpakai_ekor' => 0,
                ];
            });

        return $pembelian;
    }

    private function getProduksiPerformence(int $period, Location $location, Project $project)
    {
        $recording = Recording::with(['project', 'project.project_chick_in', 'project.fcr', 'project.fcr.fcr_standard', 'recording_depletion'])
            ->whereHas('project', fn ($query) => $query->where([
                ['project_id', $project->project_id],
                ['period', $period],
            ]))
            ->get();

        $recordingLastDay = $recording->sortByDesc('day')->first();
        $umur             = $recordingLastDay->day                                             ?? 0;
        $mortalitasStd    = $recording->pluck('project')->first()->standard_mortality ?? 0;
        $mortalitasAct    = $recordingLastDay->cum_depletion_rate                     ?? 0;
        $fcrStd           = floatval($recording->pluck('project.fcr')->first()?->fcr_standard->where('day', $umur)->first()->fcr ?? 0);
        $fcrAct           = $recording->pluck('fcr_value')->avg() ?? 0;

        $populasiAwal  = $recording->sortBy('day')->first()->total_chick ?? 1;
        $populasiAkhir = $recordingLastDay->total_chick                 ?? 1;
        $persentase    = $populasiAkhir / max($populasiAwal, 1) * 100;

        $ip = ($fcrAct > 0 && $umur > 0)
            ? intval(($persentase * ($recordingLastDay->daily_gain ?? 0)) / ($fcrAct * $umur) * 100)
            : 0; // Jika `fcrAct` atau `umur` nol, set IP ke 0

        $performence = [
            'deplesi'          => $recordingLastDay->cum_depletion ?? 0,
            'umur'             => $umur,
            'mortalitas_std'   => $mortalitasStd,
            'mortalitas_act'   => $mortalitasAct,
            'deff_mortralitas' => abs(floatval($mortalitasAct) - $mortalitasStd),
            'fcr_std'          => $fcrStd,
            'fcr_act'          => $fcrAct,
            'deff_fcr'         => abs($fcrStd - $fcrAct),
            'adg'              => $recordingLastDay->avg_daily_gain ?? 0,
            'ip'               => $ip,
        ];

        return $performence;
    }
}
