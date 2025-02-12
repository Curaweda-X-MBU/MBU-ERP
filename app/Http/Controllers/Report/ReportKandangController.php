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
use App\Models\Marketing\MarketingDeliveryVehicle;
use App\Models\Project\Project;
use App\Models\Project\ProjectBudget;
use App\Models\Project\ProjectChickIn;
use App\Models\Project\Recording;
use App\Models\Project\RecordingDepletion;
use App\Models\Project\RecordingStock;
use App\Models\Purchase\Purchase;
use App\Models\Purchase\PurchaseItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function perhitunganSapronak(Request $req, Location $location, Project $project)
    {
        try {
            $period = intval($req->query('period') ?? $project->period);

            $projectData = $project->with('project_chick_in')
                ->where([
                    ['project_id', $project->project_id],
                    ['period', $period],
                ])
                ->get();

            $purchaseDoc = $this->getPurchaseItem($period, $project, 'doc');
            $mutasiDoc   = $this->getMutasiMasuk($period, $location, $project, 'doc');

            $qtyPakaiDoc  = optional($projectData->first()->project_chick_in->first())->total_chickin ?? 0;
            $hargaBeliDoc = optional($purchaseDoc->first())->price                                    ?? 0;

            $doc = collect($purchaseDoc)->map(fn ($p) => [
                'tanggal'      => Carbon::parse($p->purchase_item_reception->last()->received_date)->format('d-M-Y H:i'),
                'no_reference' => $p->purchase->po_number ?? '-',
                'qty_masuk'    => Parser::toLocale($p->qty ?? 0),
                'qty_pakai'    => Parser::toLocale($qtyPakaiDoc ?? 0),
                'product'      => $p->product->name ?? '-',
                'harga_beli'   => Parser::toLocale($hargaBeliDoc),
                'total_harga'  => Parser::toLocale($hargaBeliDoc * $qtyPakaiDoc),
                'notes'        => $p->purchase->notes ?? null,
            ])->concat(
                collect($mutasiDoc)->map(fn ($m) => [
                    'tanggal'      => Carbon::parse($m->created_at)->format('d-M-Y H:i'),
                    'no_reference' => $m->stock_movement_id,
                    'qty_masuk'    => Parser::toLocale($m->transfer_qty),
                    'qty_pakai'    => Parser::toLocale($qtyPakaiDoc ?? 0),
                    'product'      => $m->product->name,
                    'harga_beli'   => '-',
                    'total_harga'  => '-',
                    'notes'        => $m->notes,
                ])
            )->toArray();

            $purchasePakan = $this->getPurchaseItem($period, $project, 'pakan');
            $purchaseOvk   = $this->getPurchaseItem($period, $project, 'ovk');
            $mutasiPakan   = $this->getMutasiMasuk($period, $location, $project, 'pakan');
            $mutasiOvk     = $this->getMutasiMasuk($period, $location, $project, 'ovk');

            $recordingPakan = $this->getRecordingStock($period, $project, 'pakan');
            $recordingOvk   = $this->getRecordingStock($period, $project, 'ovk');

            $mergeWithRecording = function($mutasiOrPurchase, $recordingStock) {
                $recordingQueue = collect($recordingStock);

                return collect($mutasiOrPurchase)->map(function($item) use (&$recordingQueue) {
                    $jumlahMasuk    = Parser::parseLocale($item['qty_masuk']);
                    $jumlahTerpakai = 0;
                    $finalQtyPakai  = 0;
                    $unit           = '';

                    while (! $recordingQueue->isEmpty() && $jumlahTerpakai < $jumlahMasuk) {
                        $recordingItem = $recordingQueue->shift();
                        $qtyPakai      = Parser::parseLocale($recordingItem['qty_pakai']);
                        $unit          = explode(' ', $recordingItem['qty_pakai'])[1] ?? '';

                        if ($jumlahTerpakai + $qtyPakai <= $jumlahMasuk) {
                            $jumlahTerpakai += $qtyPakai;
                            $finalQtyPakai  += $qtyPakai;
                        } else {
                            $recordingQueue->prepend([
                                'product'   => $recordingItem['product'],
                                'qty_pakai' => Parser::toLocale($qtyPakai - ($jumlahMasuk - $jumlahTerpakai)).' '.$unit,
                            ]);
                            $finalQtyPakai += ($jumlahMasuk - $jumlahTerpakai);
                            $jumlahTerpakai = $jumlahMasuk;
                        }
                    }

                    $item['qty_pakai'] = $finalQtyPakai > 0 ? Parser::toLocale($finalQtyPakai).' '.$unit : '-';

                    return $item;
                });
            };

            $pakan = $mergeWithRecording(
                collect($purchasePakan)->map(fn ($p) => [
                    'tanggal'      => Carbon::parse($p->purchase_item_reception->last()->received_date)->format('d-M-Y H:i'),
                    'no_reference' => $p->purchase->po_number,
                    'qty_masuk'    => Parser::toLocale($p->qty),
                    'qty_pakai'    => '-',
                    'product'      => $p->product->name,
                    'harga_beli'   => Parser::toLocale($p->price),
                    'total_harga'  => Parser::toLocale($p->total),
                    'notes'        => $p->purchase->notes,
                ])->concat(
                    collect($mutasiPakan)->map(fn ($m) => [
                        'tanggal'      => Carbon::parse($m->created_at)->format('d-M-Y H:i'),
                        'no_reference' => $m->stock_movement_id,
                        'qty_masuk'    => Parser::toLocale($m->transfer_qty),
                        'qty_pakai'    => '-',
                        'product'      => $m->product->name,
                        'harga_beli'   => '-',
                        'total_harga'  => '-',
                        'notes'        => $m->notes,
                    ])
                ),
                $recordingPakan
            );

            $ovk = $mergeWithRecording(
                collect($purchaseOvk)->map(fn ($p) => [
                    'tanggal'      => Carbon::parse($p->purchase_item_reception->last()->received_date)->format('d-M-Y H:i'),
                    'no_reference' => $p->purchase->po_number,
                    'qty_masuk'    => Parser::toLocale($p->qty),
                    'qty_pakai'    => '-',
                    'product'      => $p->product->name,
                    'harga_beli'   => Parser::toLocale($p->price),
                    'total_harga'  => Parser::toLocale($p->total),
                    'notes'        => $p->purchase->notes,
                ])->concat(
                    collect($mutasiOvk)->map(fn ($m) => [
                        'tanggal'      => Carbon::parse($m->created_at)->format('d-M-Y H:i'),
                        'no_reference' => $m->stock_movement_id,
                        'qty_masuk'    => Parser::toLocale($m->transfer_qty),
                        'qty_pakai'    => '-',
                        'product'      => $m->product->name,
                        'harga_beli'   => '-',
                        'total_harga'  => '-',
                        'notes'        => $m->notes,
                    ])
                ),
                $recordingOvk
            );

            return [
                'doc'   => count($doc) > 0 ? $doc : [],
                'pakan' => $pakan,
                'ovk'   => $ovk,
            ];

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getPurchaseItem(int $period, Project $project, ?string $filterProduct = null)
    {
        return PurchaseItem::with(['purchase', 'purchase_item_reception', 'purchase.warehouse.kandang.project', 'product.product_sub_category'])
            ->whereHas(
                'purchase.warehouse.kandang.project',
                fn ($q) => $q->where([
                    ['project_id', $project->project_id],
                    ['period', $period],
                ])
            )
            ->whereHas(
                'purchase',
                fn ($q) => $q->whereNotNull('po_number')
            )
            ->whereHas(
                'purchase_item_reception',
                fn ($q) => $q->whereNotNull('received_date')
            )
            ->when(
                $filterProduct,
                fn ($q) => $q->whereHas(
                    'product.product_sub_category',
                    fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($filterProduct).'%'])
                )
            )
            ->get();
    }

    public function getMutasiMasuk(int $period, Location $location, Project $project, ?string $filterProduct = null)
    {
        return StockMovement::with(['product.product_sub_category', 'product.uom', 'origin', 'destination', 'destination.kandang.project'])
            ->whereHas('destination.kandang.project', function($query) use ($project, $period) {
                $query->where([
                    ['project_id', $project->project_id],
                    ['period', $period],
                ]);
            })
            ->whereHas('destination', function($query) use ($location) {
                $query->where('location_id', $location->location_id);
            })
            ->when(
                $filterProduct,
                fn ($q) => $q->whereHas(
                    'product.product_sub_category',
                    fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($filterProduct).'%'])
                )
            )
            ->get();
    }

    private function getRecordingStock(int $period, Project $project, ?string $filterProduct = null)
    {
        return Recording::with('recording_stock.product_warehouse.product.product_sub_category')
            ->whereHas(
                'project',
                fn ($q) => $q->where([
                    ['project_id', $project->project_id],
                    ['period', $period],
                ])
            )
            ->get()
            ->flatMap(
                fn ($recording) => $recording->recording_stock
                    ->filter(fn ($rs) => str_contains(strtolower(optional($rs->product_warehouse->product->product_sub_category)->name ?? ''), $filterProduct))
                    ->map(fn ($rs) => [
                        'product'   => optional($rs->product_warehouse->product)->name ?? '-',
                        'qty_pakai' => Parser::toLocale($rs->decrease).' '.optional($rs->product_warehouse->product->uom)->name,
                    ])
            );
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

            $recording = Recording::with(['project', 'project.kandang', 'project.project_chick_in', 'project.fcr', 'project.fcr.fcr_standard', 'recording_depletion']);

            $recordingLastDayProject = $recording->whereHas('project', fn ($query) => $query->where([
                ['project_id', $project->project_id],
                ['period', $period],
            ]))
                ->get()
                ->sortByDesc('day')->first();

            $recordingLastDayLocation = Recording::join('projects', 'recordings.project_id', '=', 'projects.project_id')
                ->join('kandang', 'projects.kandang_id', '=', 'kandang.kandang_id')
                ->where('kandang.location_id', $location->location_id)
                ->whereIn('recordings.day', function($query) {
                    $query->select(DB::raw('MAX(day)'))
                        ->from('recordings')
                        ->join('projects', 'recordings.project_id', '=', 'projects.project_id')
                        ->join('kandang', 'projects.kandang_id', '=', 'kandang.kandang_id')
                        // ->whereColumn('kandang.location_id', $location->location_id)
                        ->groupBy('kandang.location_id');
                })
                ->groupBy('kandang.location_id')
                ->select([
                    'kandang.location_id',
                    DB::raw('SUM(recordings.total_chick) as total_chick_sum'),
                ])
                ->get()
                ->first();

            $expenseLocation = $this->getOverhead($location, $project, false);

            $populasiAkhirKandang = $recordingLastDayProject->total_chick ?? 0;
            $pemakaianFarm        = $expenseLocation->pluck('total')->reduce(fn ($a, $b) => Parser::parseLocale($a) + Parser::parseLocale($b), 0);
            $populasiAkhirProyek  = $recordingLastDayLocation->total_chick_sum ?? 0;

            return response()->json([
                'perhitungan' => [
                    'populasi_akhir_kandang' => $populasiAkhirKandang,
                    'pemakaian_farm'         => $pemakaianFarm,
                    'populasi_akhir_proyek'  => $populasiAkhirProyek,
                    'result'                 => $populasiAkhirProyek !== 0 ? (($populasiAkhirKandang * $pemakaianFarm) / $populasiAkhirProyek) : 0,
                ],
                'expense' => $this->getOverhead($location, $project, true),
            ]);
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

            $pembelian   = $this->getProduksiPembelian($period, $project);
            $penjualan   = $this->getProduksiPenjualan($period, $location, $project);
            $performance = $this->getProduksiPerformence($period, $project);

            return response()->json([
                'pembelian'   => $pembelian,
                'penjualan'   => $penjualan,
                'performance' => $performance,
                'selisih'     => null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function keuangan(Request $req, Location $location, Project $project)
    {
        try {
            $period = intval($req->query('period'));
            if (! $period) {
                throw new \Exception('Periode tidak ditemukan');
            }

            $bahan_baku = $this->getHppBahanBaku($project->project_id);

            return response()->json([
                'pengeluaran' => [
                    [
                        'kategori'    => 'HPP dan Pengeluaran',
                        'subkategori' => $this->getHppPembelian($project->project_id),
                    ],
                    [
                        'kategori'    => 'HPP dan Bahan Baku',
                        'subkategori' => $bahan_baku,
                    ],
                ],
                'laba_rugi' => [
                    'bruto' => [
                        $this->getTotalPenjualan($project->project_id),
                        $this->getTotalPembelian($project->project_id),
                    ],
                    'netto' => $bahan_baku,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error', $e->getMessage()], 500);
        }
    }

    private function getTotalPenjualan(int $project_id)
    {
        $penjualan = Marketing::selectRaw('
                COALESCE(SUM(marketing_products.qty), 0) as ekor,
                COALESCE(SUM(marketing_products.weight_total), 0) as kg,
                COALESCE(SUM(marketings.grand_total), 0) as grand_total
            ')
            ->join('marketing_products', 'marketing_products.marketing_id', '=', 'marketings.marketing_id')
            ->join('warehouses', 'warehouses.warehouse_id', '=', 'marketing_products.warehouse_id')
            ->join('kandang', 'kandang.kandang_id', '=', 'warehouses.kandang_id')
            ->join('projects', 'projects.kandang_id', '=', 'kandang.kandang_id')
            ->where('projects.project_id', $project_id)
            ->groupBy('kandang.location_id')
            ->get()->first();

        return [
            'id'      => 1,
            'jenis'   => 'Penjualan Ayam Besar',
            'rp_ekor' => optional($penjualan)->grand_total ?? 0 / max(optional($penjualan)->ekor, 1),
            'rp_kg'   => optional($penjualan)->grand_total ?? 0 / max(optional($penjualan)->kg, 1),
            'rp'      => optional($penjualan)->grand_total ?? 0,
        ];
    }

    private function getTotalPembelian(int $project_id)
    {
        $total_chick = $this->getTotalChick($project_id);

        $bobot_sum = $this->getBobotSum($project_id);

        $pembelian = Purchase::selectRaw('
                COALESCE(SUM(purchases.grand_total), 0) AS grand_total
            ')
            ->join('warehouses', 'warehouses.warehouse_id', '=', 'purchases.warehouse_id')
            ->join('kandang', 'kandang.kandang_id', '=', 'warehouses.kandang_id')
            ->join('projects', 'projects.kandang_id', '=', 'kandang.kandang_id')
            ->where('projects.project_id', $project_id)
            ->groupBy('kandang.location_id')
            ->get()->first();

        return [
            'id'      => 2,
            'jenis'   => 'Pembelian Sapronak Supplier',
            'rp_ekor' => $total_chick > 0 ? $pembelian->grand_total / $total_chick : 0,
            'rp_kg'   => $bobot_sum   > 0 ? $pembelian->grand_total / $bobot_sum : 0,
            'rp'      => $pembelian->grand_total,
        ];
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
                    'tanggal'      => Carbon::parse($si->created_at)->format('d-M-Y H:i'),
                    'no_referensi' => $si->stock_movement_id,
                    'transaksi'    => 'Mutasi Masuk',
                    'produk'       => $si->product->name,
                    'gudang_asal'  => $si->origin->name,
                    'qty'          => Parser::toLocale($si->transfer_qty).' '.$si->product->uom->name,
                    'notes'        => $si->notes,
                ];
            });

        $purchaseItems = Purchase::with(['warehouse.kandang.project', 'purchase_item.purchase_item_reception', 'purchase_item.product'])
            ->whereHas('warehouse.kandang.project', function($query) use ($project, $period) {
                $query->where([
                    ['project_id', $project->project_id],
                    ['period', $period],
                ]);
            })
            ->whereHas(
                'purchase_item.purchase_item_reception',
                fn ($q) => $q->whereNotNull('received_date')
            )
            ->get()
            ->flatMap(function($p) {
                return $p->purchase_item->map(function($item) use ($p) {
                    return [
                        'tanggal'      => Carbon::parse($item->purchase_item_reception->last()->received_date)->format('d-M-Y H:i'),
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
                'tanggal'       => Carbon::parse($so->created_at)->format('d-M-Y H:i'),
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
                'tanggal'       => Carbon::parse($recording->created_at)->format('d-M-Y H:i'),
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

        $averagePricePerUnit  = $totalUnit > 0 ? $totalPrice    / $totalUnit : 0;
        $averageWeightPerUnit = $totalUnit > 0 ? $totalWeightKg / $totalUnit : 0;

        return [
            'penjualan_kg'    => $totalWeightKg,
            'penjualan_ekor'  => $totalUnit,
            'bobot_rata'      => $averageWeightPerUnit,
            'harga_jual_rata' => $averagePricePerUnit,
            'total_harga'     => $totalPrice,
            'uom'             => $marketingUom,
        ];
    }

    private function getProduksiPembelian(int $period, Project $project)
    {
        $pakan_masuk_subquery = PurchaseItem::selectRaw('
                projects.project_id, COALESCE(SUM(purchase_items.qty), 0) AS pakan_masuk_qty
            ')
            ->join('products', 'products.product_id', '=', 'purchase_items.product_id')
            ->join('purchases', 'purchases.purchase_id', '=', 'purchase_items.purchase_id')
            ->join('warehouses', 'warehouses.warehouse_id', '=', 'purchases.warehouse_id')
            ->join('kandang', 'kandang.kandang_id', '=', 'warehouses.kandang_id')
            ->join('projects', 'projects.kandang_id', '=', 'kandang.kandang_id')
            ->whereRaw('LOWER(products.name) LIKE?', ['%pakan%'])
            ->groupBy('project_id');

        $pakan_mutasi_subquery = StockMovement::selectRaw('
                projects.project_id, COALESCE(SUM(stock_movements.transfer_qty), 0) AS pakan_mutasi_qty
            ')
            ->join('products', 'products.product_id', '=', 'stock_movements.product_id')
            ->join('warehouses', 'warehouses.warehouse_id', '=', 'stock_movements.destination_id')
            ->join('kandang', 'kandang.kandang_id', '=', 'warehouses.kandang_id')
            ->join('projects', 'projects.kandang_id', '=', 'kandang.kandang_id')
            ->whereRaw('LOWER(products.name) LIKE?', ['%pakan%'])
            ->groupBy('project_id');

        $pakan_terpakai_subquery = RecordingStock::selectRaw('
                projects.project_id, COALESCE(SUM(recording_stocks.decrease), 0) AS pakan_terpakai_qty
            ')
            ->join('product_warehouses', 'product_warehouses.product_warehouse_id', 'recording_stocks.product_warehouse_id')
            ->join('products', 'products.product_id', '=', 'product_warehouses.product_id')
            ->join('recordings', 'recordings.recording_id', '=', 'recording_stocks.recording_id')
            ->join('projects', 'projects.project_id', '=', 'recordings.project_id')
            ->whereRaw('LOWER(products.name) LIKE ?', ['%pakan%'])
            ->groupBy('project_id');

        $claim_culling_subquery = RecordingDepletion::selectRaw('
                projects.project_id, COALESCE(SUM(recording_depletions.total), 0) AS claim_culling
            ')
            ->join('product_warehouses', 'product_warehouses.product_warehouse_id', 'recording_depletions.product_warehouse_id')
            ->join('products', 'products.product_id', '=', 'product_warehouses.product_id')
            ->join('recordings', 'recordings.recording_id', '=', 'recording_depletions.recording_id')
            ->join('projects', 'projects.project_id', '=', 'recordings.project_id')
            // ->where('recordings.day', 1)
            // ->whereRaw('LOWER(products.name) LIKE ?', ['%culling%'])
            ->groupBy('project_id');

        $populasi_awal_subquery = ProjectChickIn::selectRaw('
                projects.project_id, COALESCE(SUM(project_chickin.total_chickin), 0) AS populasi_awal
            ')
            ->join('projects', 'projects.project_id', '=', 'project_chickin.project_id')
            ->groupBy('project_id');

        return Project::selectRaw('
                projects.project_id,
                COALESCE(SUM(populasi_awal_subquery.populasi_awal), 0) AS populasi_awal,
                COALESCE(SUM(claim_culling_subquery.claim_culling), 0) AS culling,
                COALESCE(SUM(populasi_awal_subquery.populasi_awal), 0) - COALESCE(SUM(claim_culling_subquery.claim_culling), 0) AS populasi_akhir,
                COALESCE(SUM(pakan_masuk_subquery.pakan_masuk_qty), 0) + COALESCE(SUM(pakan_mutasi_subquery.pakan_mutasi_qty), 0) AS pakan_masuk,
                COALESCE(SUM(pakan_terpakai_subquery.pakan_terpakai_qty), 0) AS pakan_terpakai,
                COALESCE(SUM(pakan_terpakai_subquery.pakan_terpakai_qty), 0) / (COALESCE(SUM(populasi_awal_subquery.populasi_awal), 0) - COALESCE(SUM(claim_culling_subquery.claim_culling), 0)) AS pakan_terpakai_per_ekor
            ')
            ->leftJoinSub($populasi_awal_subquery, 'populasi_awal_subquery', 'populasi_awal_subquery.project_id', '=', 'projects.project_id')
            ->leftJoinSub($claim_culling_subquery, 'claim_culling_subquery', 'claim_culling_subquery.project_id', '=', 'projects.project_id')
            ->leftJoinSub($pakan_masuk_subquery, 'pakan_masuk_subquery', 'pakan_masuk_subquery.project_id', '=', 'projects.project_id')
            ->leftJoinSub($pakan_mutasi_subquery, 'pakan_mutasi_subquery', 'pakan_mutasi_subquery.project_id', '=', 'projects.project_id')
            ->leftJoinSub($pakan_terpakai_subquery, 'pakan_terpakai_subquery', 'pakan_terpakai_subquery.project_id', '=', 'projects.project_id')
            ->groupBy('project_id')
            ->where('projects.project_id', $project->project_id)
            ->where('projects.period', $period)
            ->get()->first();
    }

    private function getProduksiPerformence(int $period, Project $project)
    {
        $recording = Recording::with(['project', 'project.project_chick_in', 'project.fcr', 'project.fcr.fcr_standard', 'recording_depletion'])
            ->whereHas('project', fn ($query) => $query->where([
                ['project_id', $project->project_id],
                ['period', $period],
            ]))
            ->get();

        $recordingLastDay = $recording->sortByDesc('day')->first();
        $umur             = $recordingLastDay->day                                    ?? 0;
        $mortalitasStd    = $recording->pluck('project')->first()->standard_mortality ?? 0;
        $mortalitasAct    = $recordingLastDay->cum_depletion_rate                     ?? 0;
        $fcrStd           = floatval($recording->pluck('project.fcr')->first()?->fcr_standard->where('day', $umur)->first()->fcr ?? 0);
        $fcrAct           = $recording->pluck('fcr_value')->avg() ?? 0;

        $populasiAwal  = $recording->sortBy('day')->first()->total_chick ?? 1;
        $populasiAkhir = $recordingLastDay->total_chick                  ?? 1;
        $persentase    = $populasiAkhir / max($populasiAwal, 1) * 100;

        $ip = ($fcrAct > 0 && $umur > 0)
            ? ($persentase * ($recordingLastDay->daily_gain ?? 0)) / ($fcrAct * $umur)
            : 0; // Jika `fcrAct` atau `umur` nol, set IP ke 0

        $performence = [
            'deplesi'         => $recordingLastDay->cum_depletion ?? 0,
            'umur'            => $umur,
            'mortalitas_std'  => $mortalitasStd,
            'mortalitas_act'  => $mortalitasAct,
            'deff_mortalitas' => abs(floatval($mortalitasAct) - $mortalitasStd),
            'fcr_std'         => $fcrStd,
            'fcr_act'         => $fcrAct,
            'deff_fcr'        => abs($fcrStd - $fcrAct),
            'adg'             => $recordingLastDay->avg_daily_gain ?? 0,
            'ip'              => $ip,
        ];

        return $performence;
    }

    private function getOverhead(Location $location, Project $project, bool $isProject)
    {
        $expense = Expense::with(['expense_kandang.project', 'expense_main_prices', 'expense_addit_prices'])
            ->where('location_id', $location->location_id)
            ->when($isProject, function($query) use ($project) {
                return $query->whereHas('expense_kandang.project', function($q) use ($project) {
                    $q->where([
                        ['project_id', $project->project_id],
                    ]);
                });
            })
            ->where('expense_status', 2)
            ->get()
            ->flatMap(function($e) {
                return $e->expense_main_prices->map(function($mp) {
                    return [
                        'tanggal' => Carbon::parse($mp->approved_at)->format('d-M-Y'),
                        'produk'  => $mp->nonstock->name,
                        'qty'     => "{$mp->qty} {$mp->nonstock->uom->name}",
                        'price'   => Parser::toLocale($mp->price / max($mp->qty, 1)),
                        'total'   => Parser::toLocale($mp->total_price),
                    ];
                })->concat($e->expense_addit_prices->map(function($ap) {
                    return [
                        'tanggal' => Carbon::parse($ap->approved_at)->format('d-M-Y'),
                        'produk'  => $ap->name,
                        'qty'     => '-',
                        'price'   => Parser::toLocale($ap->total_price),
                        'total'   => Parser::toLocale($ap->total_price),
                    ];
                }));
            });

        return $expense;
    }

    private function getHppPembelian(int $project_id)
    {
        // Population Initial Subquery
        $populasi_awal_subquery = ProjectChickIn::selectRaw('
                projects.project_id,
                COALESCE(SUM(project_chickin.total_chickin), 0) AS populasi_awal
            ')
            ->join('projects', 'projects.project_id', '=', 'project_chickin.project_id')
            ->groupBy('projects.project_id');

        // Purchase Items Subquery
        $produk_pembelian_subquery = PurchaseItem::selectRaw('
                projects.project_id,
                products.product_id,
                products.name AS produk,
                COALESCE(SUM(purchase_items.amount_received), 0) AS realization_total
            ')
            ->join('products', 'products.product_id', '=', 'purchase_items.product_id')
            ->join('purchases', 'purchases.purchase_id', '=', 'purchase_items.purchase_id')
            ->join('warehouses', 'warehouses.warehouse_id', '=', 'purchases.warehouse_id')
            ->join('kandang', 'kandang.kandang_id', '=', 'warehouses.kandang_id')
            ->join('projects', 'projects.kandang_id', '=', 'kandang.kandang_id')
            ->groupBy(['projects.project_id', 'products.product_id', 'products.name']);

        // Latest Recording Subquery
        $latest_day_subquery = Recording::selectRaw('
                projects.project_id,
                MAX(recordings.day) AS latest_day
            ')
            ->join('projects', 'projects.project_id', '=', 'recordings.project_id')
            ->groupBy('projects.project_id');

        $latest_recording_subquery = Recording::selectRaw('
                projects.project_id,
                recording_bw.value AS bobot,
                recordings.total_chick AS populasi_akhir
            ')
            ->join('recording_bw', 'recording_bw.recording_id', '=', 'recordings.recording_id')
            ->join('projects', 'projects.project_id', '=', 'recordings.project_id')
            ->joinSub($latest_day_subquery, 'latest_day_subquery', function($join) {
                $join->on('recordings.project_id', '=', 'latest_day_subquery.project_id')
                    ->on('recordings.day', '=', 'latest_day_subquery.latest_day');
            });

        // Budget Subquery
        $project_budget_subquery = ProjectBudget::selectRaw('
                projects.project_id,
                project_budgets.product_id,
                products.name AS budget_name,
                COALESCE(SUM(project_budgets.total), 0) AS budget_total
            ')
            ->join('projects', 'projects.project_id', '=', 'project_budgets.project_id')
            ->join('products', 'products.product_id', '=', 'project_budgets.product_id')
            ->groupBy(['projects.project_id', 'project_budgets.product_id', 'products.name']);

        // Combined Products Subquery
        $combined_products_subquery = DB::table(function($query) use ($produk_pembelian_subquery, $project_budget_subquery) {
            $query->selectRaw('
                    project_id,
                    product_id,
                    produk
                ')
                ->fromSub($produk_pembelian_subquery, 'purchase_items')
                ->union(
                    DB::table($project_budget_subquery)
                        ->selectRaw('project_id, product_id, budget_name AS produk')
                );
        }, 'combined_products');

        return Project::selectRaw('
                COALESCE(
                    SUM(produk_pembelian_subquery.realization_total) / NULLIF(SUM(populasi_awal_subquery.populasi_awal), 0)
                , 0) AS realization_rp_ekor,
                COALESCE(
                    SUM(produk_pembelian_subquery.realization_total) / NULLIF(SUM(latest_recording_subquery.bobot), 0)
                , 0) AS realization_rp_kg,
                COALESCE(SUM(produk_pembelian_subquery.realization_total), 0) AS realization_rp,
                COALESCE(
                    SUM(project_budget_subquery.budget_total) / NULLIF(SUM(populasi_awal_subquery.populasi_awal), 0)
                , 0) AS budget_rp_ekor,
                COALESCE(
                    SUM(project_budget_subquery.budget_total) / NULLIF(SUM(latest_recording_subquery.bobot), 0)
                , 0) AS budget_rp_kg,
                COALESCE(SUM(project_budget_subquery.budget_total), 0) AS budget_rp,

                combined_products.produk AS name,
                combined_products.product_id
            ')
            ->join('kandang', 'kandang.kandang_id', '=', 'projects.kandang_id')
            ->leftJoinSub($populasi_awal_subquery, 'populasi_awal_subquery', 'populasi_awal_subquery.project_id', '=', 'projects.project_id')
            ->leftJoinSub($latest_recording_subquery, 'latest_recording_subquery', 'latest_recording_subquery.project_id', '=', 'projects.project_id')
            ->leftJoinSub($combined_products_subquery, 'combined_products', function($join) {
                $join->on('combined_products.project_id', '=', 'projects.project_id');
            })
            ->leftJoinSub($produk_pembelian_subquery, 'produk_pembelian_subquery', function($join) {
                $join->on('produk_pembelian_subquery.project_id', '=', 'projects.project_id')
                    ->on('produk_pembelian_subquery.product_id', '=', 'combined_products.product_id');
            })
            ->leftJoinSub($project_budget_subquery, 'project_budget_subquery', function($join) {
                $join->on('project_budget_subquery.project_id', '=', 'projects.project_id')
                    ->on('project_budget_subquery.product_id', '=', 'combined_products.product_id');
            })
            ->where('projects.project_id', $project_id)
            ->groupBy(['combined_products.produk', 'combined_products.product_id'])
            ->get()->toArray();
    }

    private function getHppBahanBaku(int $project_id)
    {
        $total_chick = $this->getTotalChick($project_id);

        $bobot_sum = $this->getBobotSum($project_id);

        $hpp_ekspedisi = $this->getHppExpedisi($project_id)->sum('total_delivery_fee');

        return [
            $this->getHppOverhead($project_id),
            [
                'name'                => 'Beban Ekspedisi',
                'budget_rp'           => '-',
                'budget_rp_ekor'      => '-',
                'budget_rp_kg'        => '-',
                'realization_rp'      => $hpp_ekspedisi,
                'realization_rp_ekor' => $total_chick ? $hpp_ekspedisi / $total_chick : 0,
                'realization_rp_kg'   => $bobot_sum ? $hpp_ekspedisi   / $bobot_sum : 0,
            ],
        ];
    }

    private function getHppOverhead(int $project_id)
    {
        $budget = ProjectBudget::whereHas('project', fn ($p) => $p->where('project_id', $project_id))
            ->whereNotNull('nonstock_id')
            ->sum('total');

        $expense = Expense::where('category', 1)
            ->whereHas('expense_kandang.project', fn ($p) => $p->where('project_id', $project_id))
            ->get()
            ->sum('grand_total');

        $total_chick = $this->getTotalChick($project_id);

        $bobot_sum = $this->getBobotSum($project_id);

        return [
            'name'                => 'Pengeluaran Overhead',
            'budget_rp'           => $budget,
            'budget_rp_ekor'      => $total_chick ? $budget / $total_chick : 0,
            'budget_rp_kg'        => $bobot_sum ? $budget   / $bobot_sum : 0,
            'realization_rp'      => $expense,
            'realization_rp_ekor' => $total_chick ? $expense / $total_chick : 0,
            'realization_rp_kg'   => $bobot_sum ? $expense   / $bobot_sum : 0,
        ];
    }

    private function getHppExpedisi(int $project_id)
    {
        return MarketingDeliveryVehicle::selectRaw('suppliers.name AS supplier_name, SUM(marketing_delivery_vehicles.delivery_fee) AS total_delivery_fee')
            ->join('suppliers', 'suppliers.supplier_id', '=', 'marketing_delivery_vehicles.supplier_id')
            ->join('marketing_products', 'marketing_products.marketing_product_id', '=', 'marketing_delivery_vehicles.marketing_product_id')
            ->join('projects', 'projects.project_id', '=', 'marketing_products.project_id')
            ->where('projects.project_id', $project_id)
            ->groupBy('suppliers.name')
            ->get();
    }

    private function getBobotSum(int $project_id)
    {
        return Recording::whereHas('project.kandang', fn ($q) => $q
            ->whereHas('project', fn ($q) => $q->where('project_id', $project_id)))
            ->whereIn('recordings.day', function($q) {
                $q->selectRaw('MAX(day)')
                    ->from('recordings')
                    ->groupBy('project_id');
            })
            ->with('recording_bw')
            ->get()
            ->sum(fn ($recording) => optional(optional($recording)->recording_bw)->value ?? 0);
    }

    private function getTotalChick(int $project_id)
    {
        return ProjectChickIn::whereHas('project', fn ($p) => $p->where('project_id', $project_id))
            ->sum('total_chickin');
    }
}
