<?php

namespace App\Http\Controllers\Report;

use App\Constants;
use App\Helpers\Parser;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\FcrStandard;
use App\Models\DataMaster\Location;
use App\Models\Expense\Expense;
use App\Models\Inventory\StockMovement;
use App\Models\Marketing\Marketing;
use App\Models\Marketing\MarketingDeliveryVehicle;
use App\Models\Marketing\MarketingProduct;
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

class ReportLocationController extends Controller
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

    public function index(Request $req)
    {
        try {
            $company = Company::where('alias', strtoupper($req->query('company')))
                ->select(['company_id', 'name', 'alias'])
                ->first();

            if (empty($company)) {
                throw new \Exception('Invalid company');
            }

            $data = Location::where('company_id', $company->company_id)
                ->with(['kandangs.project' => function($query) {
                    $query->orderBy('period', 'desc');
                }])
                ->whereHas('kandangs.project')
                ->select(['location_id', 'name'])
                ->get()
                ->map(function($loc) {
                    $latestProject = $loc->kandangs->flatMap(function($k) {
                        return $k->project;
                    })->first();
                    if ($latestProject) {
                        $loc->project_id     = $latestProject->project_id;
                        $loc->period         = $latestProject->period;
                        $loc->farm_type      = $latestProject->farm_type;
                        $loc->count_kandang  = $loc->kandangs->count();
                        $loc->project_status = $loc->kandangs->flatMap(function($k) use ($latestProject) {
                            return $k->project->where('period', $latestProject->period);
                        })->max('project_status');
                    } else {
                        $loc->project_id     = null;
                        $loc->period         = null;
                        $loc->farm_type      = null;
                        $loc->count_kandang  = $loc->kandangs->count();
                        $loc->project_status = null;
                    }

                    return $loc;
                });

            $param = [
                'title' => "Laporan > {$company->name}",
                'data'  => $data,
            ];

            return $this->checkAccess($company, $param, 'index');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function detail(Request $req, Location $location)
    {
        try {
            $company = Company::where('alias', strtoupper($req->query('company')))
                ->select(['company_id', 'name', 'alias'])
                ->first();

            if (empty($company)) {
                throw new \Exception('Invalid company');
            }

            $input                    = $req->all();
            $kandangWithLatestProject = $location->kandangs->sortByDesc('latest_period')->first();
            $latestProject            = $kandangWithLatestProject->latest_project;
            $latestPeriod             = $latestProject->period;
            $period                   = $latestProject->period;
            $projectStatus            = $location->kandangs->flatMap(function($k) use ($period) {
                return $k->project->where('period', $period);
            })->max('project_status');
            if (isset($input['period'])) {
                $period = intval($input['period']);

                $allKandangInPeriod = $location->kandangs()->whereHas('project', fn ($p) => $p->where('period', $period));

                $kandangWithPeriodProject = (clone $allKandangInPeriod)->first();

                if ($kandangWithPeriodProject) {
                    $latestProject = $kandangWithPeriodProject->project()->where('period', $period)->first();
                }
            }

            $kandangs = $location->kandangs
                ->transform(function($k) use ($input, $period) {
                    return (object) [
                        'kandang_id'     => $k->kandang_id,
                        'name'           => $k->name,
                        'project_status' => $k->project_status,
                        'is_active'      => $k->project_status,
                        'latest_period'  => isset($input['period']) ? $period : $k->latest_period,
                        'latest_project' => isset($input['period']) ? optional($k->project())->where('period', $period)->get()->first() : ($k->latest_project ?? null),
                    ];
                });

            $projectChickinArr = $allKandangInPeriod->get()->flatMap(function($k) {
                return $k->project()->whereHas('project_chick_in')->get()->flatMap(function($p) {
                    return $p->project_chick_in->pluck('total_chickin');
                });
            });

            $detail = (object) [
                'location_id'    => $location->location_id,
                'project_id'     => 'nothing',
                'location'       => $location->name,
                'period'         => $period,
                'product'        => $latestProject ? $latestProject->product_category->name : '-',
                'doc'            => Parser::trimLocale($projectChickinArr->sum()),
                'farm_type'      => $latestProject->farm_type ?? 1,
                'project_status' => $projectStatus,
                'active_kandang' => count($kandangs->where('project_status', 1)),
                'kandangs'       => $kandangs,
                'latest_period'  => $latestPeriod,
            ];

            $param = [
                'title'  => 'Laporan > Detail',
                'detail' => $detail,
            ];

            return $this->checkAccess($company, $param, 'detail');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function sapronak(Request $req, Location $location)
    {
        try {
            $period = intval($req->query('period'));
            if (! $period) {
                throw new \Exception('Periode tidak ditemukan');
            }

            $sapronak_masuk  = $this->getSapronakMasuk($period, $location->location_id);
            $sapronak_keluar = $this->getSapronakKeluar($period, $location->location_id);

            return response()->json([
                'sapronak_masuk'  => $sapronak_masuk,
                'sapronak_keluar' => $sapronak_keluar,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function perhitunganSapronak(Request $req, Location $location)
    {
        try {
            $period = intval($req->query('period'));

            if (! $period) {
                throw new \Exception('Periode tidak ditemukan');
            }

            $projects = Project::selectRaw('
                    projects.project_id,
                    COALESCE(SUM(project_chickin.total_chickin), 0) as qty_pakai_doc
                ')
                ->join('kandang', 'kandang.kandang_id', '=', 'projects.kandang_id')
                ->join('project_chickin', 'project_chickin.project_id', '=', 'projects.project_id')
                ->where('kandang.location_id', $location->location_id)
                ->groupBy('projects.project_id')
                ->get();

            $purchaseDoc = $this->getPurchaseItem($period, $location->location_id, 'doc');
            $mutasiDoc   = $this->getMutasiMasuk($period, $location->location_id, 'doc');

            $doc = $purchaseDoc->map(function($p) use ($projects) {
                return [
                    'tanggal'      => Carbon::parse($p->po_date)->format('d-M-Y'),
                    'no_reference' => $p->po_number ?? '-',
                    'qty_masuk'    => Parser::toLocale($p->qty ?? 0),
                    'qty_pakai'    => Parser::toLocale($projects->firstWhere('project_id', $p->project_id)->qty_pakai_doc ?? 0),
                    'product'      => $p->product_name ?? '-',
                    'harga_beli'   => Parser::toLocale($p->price),
                    'total_harga'  => Parser::toLocale(($projects->firstWhere('project_id', $p->project_id)->qty_pakai_doc ?? 0) * $p->price),
                    'notes'        => $p->purchase_notes ?? null,
                ];
            })->concat($mutasiDoc->map(function($m) use ($projects) {
                return [
                    'tanggal'      => Carbon::parse($m->created_at)->format('d-M-Y'),
                    'no_reference' => $m->stock_movement_id,
                    'qty_masuk'    => Parser::toLocale($m->transfer_qty),
                    'qty_pakai'    => Parser::toLocale($projects->firstWhere('project_id', $m->project_id)->qty_pakai_doc ?? 0),
                    'product'      => $m->product_name,
                    'harga_beli'   => '-',
                    'total_harga'  => '-',
                    'notes'        => $m->notes,
                ];
            }));

            $purchasePakan = $this->getPurchaseItem($period, $location->location_id, 'pakan');
            $purchaseOvk   = $this->getPurchaseItem($period, $location->location_id, 'ovk');
            $mutasiPakan   = $this->getMutasiMasuk($period, $location->location_id, 'pakan');
            $mutasiOvk     = $this->getMutasiMasuk($period, $location->location_id, 'ovk');

            $recordingPakan = $this->getRecordingStock($period, $location->location_id, 'pakan');
            $recordingOvk   = $this->getRecordingStock($period, $location->location_id, 'ovk');

            $mergeWithRecording = function($mutasiOrPurchase, $recordingStock) {
                $recordingQueue = $recordingStock;

                return $mutasiOrPurchase->map(function($item) use (&$recordingQueue) {
                    // Convert qty_masuk to numeric value
                    $jumlahMasuk    = Parser::parseLocale($item['qty_masuk']);
                    $jumlahTerpakai = 0;
                    $finalQtyPakai  = 0;
                    $unit           = '';

                    while (! $recordingQueue->isEmpty() && $jumlahTerpakai < $jumlahMasuk) {
                        $recordingItem = $recordingQueue->shift();
                        $qtyParts      = explode(' ', $recordingItem['qty_pakai']);
                        $qtyPakai      = Parser::parseLocale($qtyParts[0]);
                        $currentUnit   = $qtyParts[1] ?? '';

                        if ($jumlahTerpakai + $qtyPakai <= $jumlahMasuk) {
                            $jumlahTerpakai += $qtyPakai;
                            $finalQtyPakai  += $qtyPakai;
                            $unit = $currentUnit;
                        } else {
                            $remaining = $jumlahMasuk - $jumlahTerpakai;
                            $recordingQueue->prepend([
                                'product'   => $recordingItem['product'],
                                'qty_pakai' => Parser::toLocale($qtyPakai - $remaining).' '.$currentUnit,
                            ]);
                            $finalQtyPakai += $remaining;
                            $jumlahTerpakai = $jumlahMasuk;
                            $unit           = $currentUnit;
                        }
                    }

                    $item['qty_pakai'] = $finalQtyPakai > 0
                        ? Parser::toLocale($finalQtyPakai).' '.$unit
                        : '-';

                    return $item;
                });
            };

            // Update pakan calculation
            $pakan = $mergeWithRecording(
                $purchasePakan->map(fn ($p) => [
                    'tanggal'      => Carbon::parse($p->po_date)->format('d-M-Y'),
                    'no_reference' => $p->po_number ?? '-',
                    'qty_masuk'    => Parser::toLocale($p->qty ?? 0),
                    'qty_pakai'    => '-',
                    'product'      => $p->product_name ?? '-',
                    'harga_beli'   => Parser::toLocale($p->price),
                    'total_harga'  => Parser::toLocale($p->price * $p->qty),
                    'notes'        => $p->purchase_notes ?? null,
                ])->concat(
                    $mutasiPakan->map(fn ($m) => [
                        'tanggal'      => Carbon::parse($m->created_at)->format('d-M-Y'),
                        'no_reference' => $m->stock_movement_id,
                        'qty_masuk'    => Parser::toLocale($m->transfer_qty),
                        'qty_pakai'    => '-',
                        'product'      => $m->product_name,
                        'harga_beli'   => '-',
                        'total_harga'  => '-',
                        'notes'        => $m->notes,
                    ])
                ),
                collect($recordingPakan)
            );

            $ovk = $mergeWithRecording(
                $purchaseOvk->map(fn ($p) => [
                    'tanggal'      => Carbon::parse($p->po_date)->format('d-M-Y'),
                    'no_reference' => $p->po_number,
                    'qty_masuk'    => Parser::toLocale($p->qty),
                    'qty_pakai'    => '-',
                    'product'      => $p->product_name,
                    'harga_beli'   => Parser::toLocale($p->price),
                    'total_harga'  => Parser::toLocale($p->total),
                    'notes'        => $p->notes,
                ])->concat(
                    $mutasiOvk->map(fn ($m) => [
                        'tanggal'      => Carbon::parse($m->created_at)->format('d-M-Y'),
                        'no_reference' => $m->stock_movement_id,
                        'qty_masuk'    => Parser::toLocale($m->transfer_qty),
                        'qty_pakai'    => '-',
                        'product'      => $m->product_name,
                        'harga_beli'   => '-',
                        'total_harga'  => '-',
                        'notes'        => $m->notes,
                    ])
                ),
                $recordingOvk
            );

            return response()->json([
                'doc'   => $doc,
                'ovk'   => $ovk,
                'pakan' => $pakan,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function penjualan(Request $req, Location $location)
    {
        try {
            $period = intval($req->query('period'));
            if (! $period) {
                throw new \Exception('Periode tidak ditemukan');
            }

            $marketings = Marketing::with(
                [
                    'marketing_products.product.uom',
                    'marketing_products.warehouse.kandang',
                    'marketing_addit_prices',
                    'customer',
                    'marketing_products.project.project_chick_in' => function($ci) {
                        $ci->orderBy('chickin_date');
                    },
                ]
            )
                ->where([
                    ['marketing_status', '>=', 3],
                    ['marketing_return_id', '=', null],
                ])
                ->whereHas('marketing_products.project', function($p) use ($period) {
                    $p->where('period', $period);
                })->whereHas('marketing_products.warehouse.kandang', function($k) use ($location) {
                    $k->where('location_id', $location->location_id);
                })
                ->get()
                ->transform(function($m) {
                    $chickinDate = $m->marketing_products
                        ->sortByDesc('project.first_day_old_chick')
                        ->first()
                        ->project
                        ->first_day_old_chick;

                    $chickinParsed = Carbon::parse($chickinDate);
                    $umur          = $m->realized_at ? $chickinParsed->diffInDays(Carbon::parse($m->realized_at)) : '-';

                    return [
                        'tanggal'                => $m->realized_at ? date('d-M-Y', strtotime($m->realized_at)) : '-',
                        'umur'                   => $umur,
                        'no_do'                  => $m->id_marketing,
                        'customer'               => $m->customer->name,
                        'jumlah_ekor'            => $m->marketing_products->sum('qty'),
                        'jumlah_kg'              => $m->marketing_products->sum('weight_total'),
                        'harga'                  => Parser::toLocale($m->sub_total),
                        'cn'                     => Parser::toLocale($m->not_paid),
                        'total'                  => Parser::toLocale($m->grand_total),
                        'kandang'                => $m->marketing_products->flatMap(fn ($mp) => $mp->warehouse->kandang->only('kandang_id'))->unique()->count(),
                        'status'                 => [$m->payment_status, Constants::MARKETING_PAYMENT_STATUS[$m->payment_status]],
                        'marketing_products'     => $m->marketing_products,
                        'marketing_addit_prices' => $m->marketing_addit_prices,
                    ];
                });

            return response()->json($marketings);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 500]);
        }
    }

    public function overhead(Request $req, Location $location)
    {
        try {
            $period = intval($req->query('period'));
            if (! $period) {
                throw new \Exception('Periode tidak ditemukan');
            }

            // NOTE:: MAKE RAW QUERY, SELECT FROM PROJECT WHERE LOCATION

            $expenses = Expense::with([
                'expense_main_prices:expense_item_id,expense_id,sub_category,qty,uom,price',
                'expense_addit_prices:expense_addit_price_id,expense_id,name,price',
                'expense_kandang.project.project_budget.nonstock' => function($query) {
                    $query->select('nonstock_id', 'name'); // Load only necessary fields
                },
            ])
                ->where('location_id', $location->location_id)
                ->where('expense_status', 2)
                ->whereIn('category', [1, 2])
                ->where(function($q) use ($period) {
                    $q->where('category', 2)
                        ->orWhereHas('expense_kandang.project', function($p) use ($period) {
                            $p->where('period', $period);
                        });
                })
                ->get();

            $formatted_expenses = collect($expenses)->flatMap(function($e) {
                return collect($e->expense_main_prices)
                    ->concat($e->expense_addit_prices)
                    ->map(function($p) use ($e) {
                        $product_name = strtolower($p->sub_category ?? $p->name);
                        $budget       = null;

                        foreach ($e->expense_kandang as $k) {
                            $budget = optional(optional($k->project)->project_budget)
                                ->firstWhere(fn ($b) => str_contains(strtolower(optional($b->nonstock)->name), $product_name));
                            if ($budget) {
                                break;
                            }
                        }

                        return [
                            'produk'            => $p->sub_category ?? "{$p->name} (Lainnya)",
                            'tanggal'           => Carbon::parse($p->expense->approved_at)->format('d-M-Y'),
                            'no_ref'            => '####', // dummy
                            'budget_qty'        => $budget->qty ?? '-',
                            'budget_price'      => isset($budget->price) ? Parser::toLocale($budget->price) : '-',
                            'budget_total'      => isset($budget->total) ? Parser::toLocale($budget->total) : '-',
                            'realization_qty'   => ($p->total_qty ?? $p->qty) ?? '-',
                            'uom'               => $p->uom                    ?? '',
                            'realization_price' => Parser::toLocale($p->price),
                            'realization_total' => Parser::toLocale($p->total_price),
                            'price_per_qty'     => $p->qty ? Parser::toLocale($p->price / ($p->qty ?? 1)) : '-',
                            'category'          => $e->category,
                        ];
                    });
            });

            $grouped_expenses = $formatted_expenses->groupBy('produk')->collapse();

            $bop_expenses  = $grouped_expenses->filter(fn ($item) => $item['category'] == 1)->values();
            $nbop_expenses = $grouped_expenses->filter(fn ($item) => $item['category'] == 2)->values();

            return response()->json([
                [
                    'kategori'    => 'Pengeluaran Operasional',
                    'subkategori' => $bop_expenses,
                ],
                [
                    'kategori'    => 'Pengeluaran Bukan Operasional',
                    'subkategori' => $nbop_expenses,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 500]);
        }
    }

    public function hppEkspedisi(Request $req, Location $location)
    {
        try {
            $period = intval($req->query('period'));
            if (! $period) {
                throw new \Exception('Periode tidak ditemukan');
            }

            $deliveryVehicles = $this->getHppExpedisi($period, $location->location_id);

            return response()->json($deliveryVehicles);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 500]);
        }
    }

    public function dataProduksi(Request $req, Location $location)
    {
        try {
            $period = intval($req->query('period'));
            if (! $period) {
                throw new \Exception('Periode tidak ditemukan');
            }

            $pembelian   = $this->getDataProduksiPembelian($period, $location->location_id);
            $penjualan   = $this->getDataProduksiPenjualan($period, $location->location_id);
            $performance = $this->getDataProduksiPerformance($period, $location->location_id);

            return response()->json([
                'pembelian'   => $pembelian,
                'penjualan'   => $penjualan,
                'performance' => $performance,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error', $e->getMessage()], 500);
        }
    }

    public function keuangan(Request $req, Location $location)
    {
        try {
            $period = intval($req->query('period'));
            if (! $period) {
                throw new \Exception('Periode tidak ditemukan');
            }

            $bahan_baku = $this->getHppBahanBaku($period, $location->location_id);

            return response()->json([
                'pengeluaran' => [
                    [
                        'kategori'    => 'HPP dan Pengeluaran',
                        'subkategori' => $this->getHppPembelian($period, $location->location_id),
                    ],
                    [
                        'kategori'    => 'HPP dan Bahan Baku',
                        'subkategori' => $bahan_baku,
                    ],
                ],
                'laba_rugi' => [
                    'bruto' => [
                        $this->getTotalPenjualan($period, $location->location_id),
                        $this->getTotalPembelian($period, $location->location_id),
                    ],
                    'netto' => $bahan_baku,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error', $e->getMessage()], 500);
        }
    }

    private function getTotalPenjualan(int $period, int $location_id)
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
            ->where('kandang.location_id', $location_id)
            ->where('projects.period', $period)
            ->groupBy('kandang.location_id')
            ->get()->first();

        return [
            'id'      => 1,
            'jenis'   => 'Penjualan Ayam Besar',
            'rp_ekor' => $penjualan->grand_total / $penjualan->ekor,
            'rp_kg'   => $penjualan->grand_total / $penjualan->kg,
            'rp'      => $penjualan->grand_total,
        ];
    }

    private function getTotalPembelian(int $period, int $location_id)
    {
        $total_chick = $this->getTotalChick($period, $location_id);

        $bobot_sum = $this->getBobotSum($period, $location_id);

        $pembelian = Purchase::selectRaw('
                COALESCE(SUM(purchases.grand_total), 0) AS grand_total
            ')
            ->join('warehouses', 'warehouses.warehouse_id', '=', 'purchases.warehouse_id')
            ->join('kandang', 'kandang.kandang_id', '=', 'warehouses.kandang_id')
            ->join('projects', 'projects.kandang_id', '=', 'kandang.kandang_id')
            ->where('kandang.location_id', $location_id)
            ->where('projects.period', $period)
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

    private function getSapronakMasuk(int $period, int $location_id)
    {
        $stockMovement = StockMovement::selectRaw('
                stock_movements.transfer_qty AS num_qty,
                uom.name AS uom,
                DATE_FORMAT(stock_movements.created_at, "%d-%b-%Y") AS tanggal,
                stock_movements.stock_movement_id AS no_referensi,
                "Mutasi Masuk" as transaksi,
                products.name AS produk,
                warehouses_origin.name AS gudang_asal,
                stock_movements.notes AS notes
            ')
            ->join('products', 'products.product_id', '=', 'stock_movements.product_id')
            ->join('uom', 'uom.uom_id', '=', 'products.uom_id')
            ->join('warehouses AS warehouses_destination', 'warehouses_destination.warehouse_id', '=', 'stock_movements.destination_id')
            ->join('warehouses AS warehouses_origin', 'warehouses_origin.warehouse_id', '=', 'stock_movements.origin_id')
            ->join('kandang', 'kandang.kandang_id', '=', 'warehouses_destination.kandang_id')
            ->join('projects', 'projects.kandang_id', '=', 'kandang.kandang_id')
            ->where([
                ['kandang.location_id', $location_id],
                ['projects.period', $period],
                ['stock_movements.product_id', '!=', null],
            ])
            ->groupByRaw('
                    stock_movements.created_at,
                    stock_movements.stock_movement_id,
                    products.name,
                    warehouses_origin.name,
                    stock_movements.transfer_qty,
                    uom.name,
                    stock_movements.notes
                ')
            ->get()
            ->map(function($sm) {
                $sm->qty = Parser::toLocale($sm->num_qty).' '.$sm->uom;

                return $sm;
            });

        $purchaseItems = PurchaseItem::selectRaw('
                purchase_items.qty AS num_qty,
                uom.name AS uom,
                purchases.approval_line,
                COALESCE(purchases.po_number, purchases.pr_number) AS no_referensi,
                "Pembelian" AS transaksi,
                products.name AS produk,
                "-" AS gudang_asal,
                purchases.notes AS notes,
                purchase_items.price AS harga_satuan
            ')
            ->join('purchases', 'purchases.purchase_id', '=', 'purchase_items.purchase_id')
            ->join('products', 'products.product_id', '=', 'purchase_items.product_id')
            ->join('uom', 'uom.uom_id', '=', 'products.uom_id')
            ->join('warehouses', 'warehouses.warehouse_id', '=', 'purchases.warehouse_id')
            ->join('kandang', 'kandang.kandang_id', '=', 'warehouses.kandang_id')
            ->join('projects', 'projects.kandang_id', '=', 'kandang.kandang_id')
            ->where([
                ['kandang.location_id', $location_id],
                ['projects.period', $period],
            ])
            ->groupByRaw('
                    purchases.approval_line,
                    purchases.pr_number,
                    purchases.po_number,
                    products.name,
                    purchase_items.qty,
                    uom.name,
                    purchases.notes,
                    purchase_items.price
                ')
            ->get()
            ->map(function($pi) {
                $approval    = collect(json_decode($pi->approval_line))->firstWhere('status', 3);
                $pi->tanggal = $approval ? Carbon::parse($approval->date)->format('d-M-Y') : '-';
                $pi->qty     = Parser::toLocale($pi->num_qty).' '.$pi->uom;

                return $pi;
            });

        return $stockMovement->concat($purchaseItems);
    }

    private function getSapronakKeluar(int $period, int $location_id)
    {
        $stockMovement = StockMovement::selectRaw('
                stock_movements.transfer_qty AS num_qty,
                uom.name AS uom,
                DATE_FORMAT(stock_movements.created_at, "%d-%b-%Y") AS tanggal,
                stock_movements.stock_movement_id AS no_referensi,
                "Mutasi Keluar" as transaksi,
                products.name AS produk,
                warehouses_destination.name AS gudang_tujuan,
                stock_movements.notes AS notes
            ')
            ->join('products', 'products.product_id', '=', 'stock_movements.product_id')
            ->join('uom', 'uom.uom_id', '=', 'products.uom_id')
            ->join('warehouses AS warehouses_origin', 'warehouses_origin.warehouse_id', '=', 'stock_movements.origin_id')
            ->join('warehouses AS warehouses_destination', 'warehouses_destination.warehouse_id', '=', 'stock_movements.destination_id')
            ->join('kandang', 'kandang.kandang_id', '=', 'warehouses_origin.kandang_id')
            ->join('projects', 'projects.kandang_id', '=', 'kandang.kandang_id')
            ->where([
                ['kandang.location_id', $location_id],
                ['projects.period', $period],
                ['stock_movements.product_id', '!=', null],
            ])
            ->groupByRaw('
                    stock_movements.created_at,
                    stock_movements.stock_movement_id,
                    products.name,
                    warehouses_destination.name,
                    stock_movements.transfer_qty,
                    uom.name,
                    stock_movements.notes
                ')
            ->get()
            ->map(function($sm) {
                $sm->qty = Parser::toLocale($sm->num_qty).' '.$sm->uom;

                return $sm;
            });

        $recordingStockQuery = Recording::selectRaw('
                recording_stocks.decrease AS num_qty,
                uom.name AS uom,
                recordings.record_datetime AS tanggal,
                DATE_FORMAT(recordings.record_datetime, "%d-%b-%Y %H:%i:%s") AS formatted_tanggal,
                recordings.recording_id AS no_referensi,
                "Recording (Persediaan)" AS transaksi,
                products.name AS produk,
                "-" AS gudang_tujuan,
                recording_stocks.notes AS notes
            ')
            ->join('recording_stocks', 'recording_stocks.recording_id', '=', 'recordings.recording_id')
            ->join('product_warehouses AS pw_stock', 'pw_stock.product_warehouse_id', '=', 'recording_stocks.product_warehouse_id')
            ->join('products', 'products.product_id', '=', 'pw_stock.product_id')
            ->leftJoin('uom', 'uom.uom_id', '=', 'products.uom_id')
            ->join('projects', 'projects.project_id', '=', 'recordings.project_id')
            ->join('kandang', 'kandang.kandang_id', '=', 'projects.kandang_id')
            ->where([
                ['kandang.location_id', $location_id],
                ['projects.period', $period],
            ]);

        $recordingDepletionQuery = Recording::selectRaw('
                recording_depletions.total AS num_qty,
                uom.name AS uom,
                recordings.record_datetime AS tanggal,
                DATE_FORMAT(recordings.record_datetime, "%d-%b-%Y %H:%i:%s") AS formatted_tanggal,
                recordings.recording_id AS no_referensi,
                "Recording (Deplesi)" AS transaksi,
                products.name AS produk,
                "-" AS gudang_tujuan,
                recording_depletions.notes AS notes
            ')
            ->join('recording_depletions', 'recording_depletions.recording_id', '=', 'recordings.recording_id')
            ->join('product_warehouses AS pw_depletion', 'pw_depletion.product_warehouse_id', '=', 'recording_depletions.product_warehouse_id')
            ->join('products', 'products.product_id', '=', 'pw_depletion.product_id')
            ->leftJoin('uom', 'uom.uom_id', '=', 'products.uom_id')
            ->join('projects', 'projects.project_id', '=', 'recordings.project_id')
            ->join('kandang', 'kandang.kandang_id', '=', 'projects.kandang_id')
            ->where([
                ['kandang.location_id', $location_id],
                ['projects.period', $period],
            ]);

        $recordingEggQuery = Recording::selectRaw('
                recording_eggs.total AS num_qty,
                uom.name AS uom,
                recordings.record_datetime AS tanggal,
                DATE_FORMAT(recordings.record_datetime, "%d-%b-%Y %H:%i:%s") AS formatted_tanggal,
                recordings.recording_id AS no_referensi,
                "Recording (Telur)" AS transaksi,
                products.name AS produk,
                "-" AS gudang_tujuan,
                recording_eggs.notes AS notes
            ')
            ->join('recording_eggs', 'recording_eggs.recording_id', '=', 'recordings.recording_id')
            ->join('product_warehouses AS pw_egg', 'pw_egg.product_warehouse_id', '=', 'recording_eggs.product_warehouse_id')
            ->join('products', 'products.product_id', '=', 'pw_egg.product_id')
            ->leftJoin('uom', 'uom.uom_id', '=', 'products.uom_id')
            ->join('projects', 'projects.project_id', '=', 'recordings.project_id')
            ->join('kandang', 'kandang.kandang_id', '=', 'projects.kandang_id')
            ->where([
                ['kandang.location_id', $location_id],
                ['projects.period', $period],
            ]);

        $recordingItems = $recordingStockQuery->unionAll($recordingDepletionQuery)->unionAll($recordingEggQuery)->get()
            ->map(function($item) {
                $item->qty = Parser::toLocale($item->num_qty).' '.$item->uom;

                return $item;
            });

        return $stockMovement->concat($recordingItems);
    }

    private function getDataProduksiPembelian(int $period, int $location_id)
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
            ->whereRaw('LOWER(products.name) LIKE ?', ['%pakan%'])
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
            ->where('recordings.day', 1)
            ->whereRaw('LOWER(products.name) LIKE ?', ['%culling%'])
            ->groupBy('project_id');

        $populasi_awal_subquery = ProjectChickIn::selectRaw('
                projects.project_id, COALESCE(SUM(project_chickin.total_chickin), 0) AS populasi_awal
            ')
            ->join('projects', 'projects.project_id', '=', 'project_chickin.project_id')
            ->groupBy('project_id');

        return Project::selectRaw('
                kandang.location_id,
                COALESCE(SUM(populasi_awal_subquery.populasi_awal), 0) AS populasi_awal,
                COALESCE(SUM(claim_culling_subquery.claim_culling), 0) AS culling,
                COALESCE(SUM(populasi_awal_subquery.populasi_awal), 0) - COALESCE(SUM(claim_culling_subquery.claim_culling), 0) AS populasi_akhir,
                COALESCE(SUM(pakan_masuk_subquery.pakan_masuk_qty), 0) + COALESCE(SUM(pakan_mutasi_subquery.pakan_mutasi_qty), 0) AS pakan_masuk,
                COALESCE(SUM(pakan_terpakai_subquery.pakan_terpakai_qty), 0) AS pakan_terpakai,
                COALESCE(COALESCE(SUM(pakan_terpakai_subquery.pakan_terpakai_qty), 0) / (COALESCE(SUM(populasi_awal_subquery.populasi_awal), 0) - COALESCE(SUM(claim_culling_subquery.claim_culling), 0)), 0) AS pakan_terpakai_per_ekor
            ')
            ->join('kandang', 'kandang.kandang_id', '=', 'projects.kandang_id')
            ->leftJoinSub($populasi_awal_subquery, 'populasi_awal_subquery', 'populasi_awal_subquery.project_id', '=', 'projects.project_id')
            ->leftJoinSub($claim_culling_subquery, 'claim_culling_subquery', 'claim_culling_subquery.project_id', '=', 'projects.project_id')
            ->leftJoinSub($pakan_masuk_subquery, 'pakan_masuk_subquery', 'pakan_masuk_subquery.project_id', '=', 'projects.project_id')
            ->leftJoinSub($pakan_mutasi_subquery, 'pakan_mutasi_subquery', 'pakan_mutasi_subquery.project_id', '=', 'projects.project_id')
            ->leftJoinSub($pakan_terpakai_subquery, 'pakan_terpakai_subquery', 'pakan_terpakai_subquery.project_id', '=', 'projects.project_id')
            ->groupBy('location_id')
            ->where('kandang.location_id', $location_id)
            ->where('projects.period', $period)
            ->get()->first();
    }

    private function getDataProduksiPenjualan(int $period, int $location_id)
    {
        $marketing_products_subquery = MarketingProduct::selectRaw('
                projects.project_id,
                marketing_products.qty,
                marketing_products.weight_total,
                marketing_products.total_price,
                marketing_products.weight_avg AS weight_avg,
                marketing_products.price,
                uom.name AS uom
            ')
            ->join('uom', 'uom.uom_id', '=', 'marketing_products.uom_id')
            ->join('warehouses', 'warehouses.warehouse_id', '=', 'marketing_products.warehouse_id')
            ->join('kandang', 'kandang.kandang_id', '=', 'warehouses.kandang_id')
            ->join('projects', 'projects.kandang_id', '=', 'kandang.kandang_id');

        return Project::selectRaw('
                kandang.location_id,
                COALESCE(SUM(marketing_products_subquery.qty), 0) AS penjualan_ekor,
                COALESCE(SUM(marketing_products_subquery.weight_total), 0) AS penjualan_kg,
                COALESCE(SUM(marketing_products_subquery.total_price), 0) AS total_harga,
                COALESCE(AVG(marketing_products_subquery.weight_avg), 0) AS bobot_rata,
                COALESCE(AVG(marketing_products_subquery.price), 0) AS harga_jual_rata,
                COALESCE(MIN(marketing_products_subquery.uom), "-") AS uom
            ')
            ->join('kandang', 'kandang.kandang_id', '=', 'projects.kandang_id')
            ->leftJoinSub($marketing_products_subquery, 'marketing_products_subquery', 'marketing_products_subquery.project_id', '=', 'projects.project_id')
            ->where('kandang.location_id', $location_id)
            ->where('projects.period', $period)
            ->groupBy('location_id')
            ->get()->first();
    }

    private function getDataProduksiPerformance(int $period, int $location_id)
    {
        $latest_day_subquery = Recording::selectRaw('
            projects.project_id,
            MAX(recordings.day) AS latest_day
        ')
            ->join('projects', 'projects.project_id', '=', 'recordings.project_id')
            ->groupBy('projects.project_id');

        $latest_recording_subquery = Recording::selectRaw('
            projects.project_id,
            recordings.day AS umur,
            recordings.cum_depletion_rate AS mortalitas_act,
            recordings.cum_depletion,
            recordings.fcr_value AS fcr_act,
            recordings.avg_daily_gain,
            recordings.daily_gain,
            recordings.total_chick AS populasi_akhir
        ')
            ->join('projects', 'projects.project_id', '=', 'recordings.project_id')
            ->joinSub($latest_day_subquery, 'latest_day_subquery', function($join) {
                $join->on('recordings.project_id', '=', 'latest_day_subquery.project_id')
                    ->on('recordings.day', '=', 'latest_day_subquery.latest_day');
            });

        $fcr_std_subquery = FcrStandard::selectRaw('
            projects.project_id,
            fcr_standards.day AS fcr_day,
            fcr_standards.fcr AS fcr_std
        ')
            ->join('fcr', 'fcr.fcr_id', '=', 'fcr_standards.fcr_id')
            ->join('projects', 'projects.fcr_id', '=', 'fcr.fcr_id')
            ->joinSub($latest_day_subquery, 'latest_day_subquery', function($join) {
                $join->on('projects.project_id', '=', 'latest_day_subquery.project_id')
                    ->on('fcr_standards.day', '=', 'latest_day_subquery.latest_day'); // ✅ Match with latest recording day
            });

        $populasi_awal_subquery = ProjectChickIn::selectRaw('
                projects.project_id, COALESCE(SUM(project_chickin.total_chickin), 0) AS populasi_awal
            ')
            ->join('projects', 'projects.project_id', '=', 'project_chickin.project_id')
            ->groupBy('project_id');

        $performance = Project::selectRaw('
            kandang.location_id,
            COALESCE(AVG(latest_recording_subquery.umur), 0) AS umur,
            COALESCE(AVG(latest_recording_subquery.mortalitas_act), 0) AS mortalitas_act,
            COALESCE(AVG(latest_recording_subquery.fcr_act), 0) AS fcr_act,
            COALESCE(SUM(latest_recording_subquery.cum_depletion), 0) AS deplesi,
            COALESCE(AVG(latest_recording_subquery.avg_daily_gain), 0) AS adg,
            COALESCE(AVG(latest_recording_subquery.daily_gain), 0) AS daily_gain,
            COALESCE(SUM(latest_recording_subquery.populasi_akhir), 0) AS populasi_akhir,
            COALESCE(SUM(populasi_awal_subquery.populasi_awal), 0) AS populasi_awal,
            COALESCE(AVG(projects.standard_mortality), 0) AS mortalitas_std,
            COALESCE(MAX(fcr_std_subquery.fcr_day), 0) AS fcr_day,
            COALESCE(AVG(fcr_std_subquery.fcr_std), 0) AS fcr_std
        ')
            ->join('kandang', 'kandang.kandang_id', '=', 'projects.kandang_id')
            ->leftJoinSub($latest_recording_subquery, 'latest_recording_subquery', function($join) {
                $join->on('latest_recording_subquery.project_id', '=', 'projects.project_id');
            })
            ->leftJoinSub($fcr_std_subquery, 'fcr_std_subquery', function($join) {
                $join->on('fcr_std_subquery.project_id', '=', 'projects.project_id');
            })
            ->leftJoinSub($populasi_awal_subquery, 'populasi_awal_subquery', function($join) {
                $join->on('populasi_awal_subquery.project_id', '=', 'projects.project_id');
            })
            ->where('kandang.location_id', $location_id)
            ->where('projects.period', $period)
            ->groupBy('kandang.location_id')
            ->get()->first();

        $performance->deff_mortalitas = abs(floatval($performance->mortalitas_std) - $performance->mortalitas_act);
        $performance->deff_fcr        = abs(floatval($performance->fcr_std) - $performance->fcr_act);

        $persentase      = $performance->populasi_akhir / max($performance->populasi_awal, 1) * 100;
        $performance->ip = ($performance->fcr_act > 0 && $performance->umur > 0)
        ? intval($persentase * $performance->daily_gain) / ($performance->fcr_act * $performance->umur) * 100
        : 0;

        return $performance;
    }

    private function getHppPembelian(int $period, int $location_id)
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
            ->where('kandang.location_id', $location_id)
            ->where('projects.period', $period)
            ->groupBy(['combined_products.produk', 'combined_products.product_id'])
            ->get()->toArray();
    }

    private function getHppBahanBaku(int $period, int $location_id)
    {
        $total_chick = $this->getTotalChick($period, $location_id);

        $bobot_sum = $this->getBobotSum($period, $location_id);

        $hpp_ekspedisi = $this->getHppExpedisi($period, $location_id)->sum('total_delivery_fee');

        return [
            $this->getHppOverhead($period, $location_id),
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

    private function getHppOverhead(int $period, int $location_id)
    {
        $budget = ProjectBudget::whereHas('project', fn ($p) => $p->where('period', $period))
            ->whereHas('project.kandang', fn ($k) => $k->where('location_id', $location_id))
            ->whereNotNull('nonstock_id')
            ->sum('total');

        $expense = Expense::where('category', 1)
            ->where('location_id', $location_id)
            ->whereHas('expense_kandang.project', fn ($p) => $p->where('period', $period))
            ->get()
            ->sum('grand_total');

        $total_chick = $this->getTotalChick($period, $location_id);

        $bobot_sum = $this->getBobotSum($period, $location_id);

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

    private function getHppExpedisi(int $period, int $location_id)
    {
        return MarketingDeliveryVehicle::selectRaw('suppliers.name AS supplier_name, SUM(marketing_delivery_vehicles.delivery_fee) AS total_delivery_fee')
            ->join('suppliers', 'suppliers.supplier_id', '=', 'marketing_delivery_vehicles.supplier_id')
            ->join('marketing_products', 'marketing_products.marketing_product_id', '=', 'marketing_delivery_vehicles.marketing_product_id')
            ->join('projects', 'projects.project_id', '=', 'marketing_products.project_id')
            ->whereIn('projects.kandang_id', function($query) use ($location_id) {
                $query->select('kandang_id')->from('kandang')->where('location_id', $location_id);
            })
            ->where('projects.period', $period)
            ->groupBy('suppliers.name')
            ->get();
    }

    private function getBobotSum(int $period, int $location_id)
    {
        return Recording::whereHas('project.kandang', fn ($q) => $q
            ->where('location_id', $location_id))
            ->whereHas('project', fn ($q) => $q->where('period', $period))
            ->whereIn('recordings.day', function($q) {
                $q->selectRaw('MAX(day)')
                    ->from('recordings')
                    ->groupBy('project_id');
            })
            ->with('recording_bw')
            ->get()
            ->sum(fn ($recording) => optional(optional($recording)->recording_bw)->value ?? 0);
    }

    private function getTotalChick(int $period, int $location_id)
    {
        return ProjectChickIn::whereHas('project', fn ($p) => $p->where('period', $period))
            ->whereHas('project.kandang', fn ($k) => $k->where('location_id', $location_id))
            ->sum('total_chickin');
    }

    private function getPurchaseItem(int $period, int $location_id, ?string $filter_product = null, ?int $product_id = null)
    {
        $query = DB::table('purchase_items')
            ->select([
                'purchase_items.*',
                'purchases.po_number',
                'purchases.po_date',
                'purchases.notes as purchase_notes',
                'products.name as product_name',
                'purchase_items.price',
                'projects.project_id',
            ])
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.purchase_id')
            ->join('products', 'purchase_items.product_id', '=', 'products.product_id')
            ->join('warehouses', 'purchases.warehouse_id', '=', 'warehouses.warehouse_id')
            ->join('kandang', 'warehouses.kandang_id', '=', 'kandang.kandang_id')
            ->join('projects', 'kandang.kandang_id', '=', 'projects.kandang_id')
            ->where([
                ['kandang.location_id', '=', $location_id],
                ['projects.period', '=', $period],
            ])
            ->where(function($query) {
                $query->whereNotNull('purchases.po_number')
                    ->orWhereNotNull('purchases.po_date');
            });

        if ($product_id) {
            $query->where('purchase_items.product_id', '=', $product_id);
        }

        if ($filter_product) {
            $query->whereRaw('LOWER(products.name) LIKE ?', [strtolower('%'.$filter_product.'%')]);
        }

        return $query->get();
    }

    public function getMutasiMasuk(int $period, int $location_id, ?string $filterProduct = null, ?int $productId = null)
    {
        $query = DB::table('stock_movements')
            ->select([
                'stock_movements.*',
                'warehouses.location_id',
                'products.name as product_name',
                'kandang.kandang_id',
                'stock_movements.notes',
                'projects.project_id',
            ])
            ->join('warehouses', 'stock_movements.destination_id', '=', 'warehouses.warehouse_id')
            ->join('products', 'stock_movements.product_id', '=', 'products.product_id')
            ->join('kandang', 'warehouses.kandang_id', '=', 'kandang.kandang_id')
            ->join('projects', 'kandang.kandang_id', '=', 'projects.kandang_id')
            ->where('kandang.location_id', $location_id)
            ->where('projects.period', $period);

        if ($productId) {
            $query->where('stock_movements.product_id', $productId);
        }

        if ($filterProduct) {
            $query->whereExists(function($subQuery) use ($filterProduct) {
                $subQuery->select(DB::raw(1))
                    ->from('products')
                    ->whereColumn('products.product_id', 'stock_movements.product_id')
                    ->whereRaw('LOWER(products.name) LIKE ?', [strtolower('%'.$filterProduct.'%')]);
            });
        }

        return $query->get();
    }

    private function getRecordingStock(int $period, int $location_id, ?string $filterProduct = null)
    {
        $query = DB::table('recordings')
            ->select([
                'products.name as product_name',
                'recording_stocks.decrease as qty_pakai',
                'uom.name as uom_name',
            ])
            ->join('projects', 'recordings.project_id', '=', 'projects.project_id')
            ->join('kandang', 'projects.kandang_id', '=', 'kandang.kandang_id')
            ->join('recording_stocks', 'recordings.recording_id', '=', 'recording_stocks.recording_id')
            ->join('product_warehouses', 'recording_stocks.product_warehouse_id', '=', 'product_warehouses.product_warehouse_id')
            ->join('products', 'product_warehouses.product_id', '=', 'products.product_id')
            ->leftJoin('uom', 'products.uom_id', '=', 'uom.uom_id')
            ->where('kandang.location_id', $location_id)
            ->where('projects.period', $period);

        if ($filterProduct) {
            $query->whereRaw('LOWER(products.name) LIKE ?', [strtolower('%'.$filterProduct.'%')]);
        }

        return $query->get()
            ->map(function($item) {
                return [
                    'product'   => $item->product_name ?? '-',
                    'qty_pakai' => Parser::toLocale($item->qty_pakai).' '.($item->uom_name ?? ''),
                ];
            });
    }
}
