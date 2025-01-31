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

            $input                    = $req->all();
            $kandangWithLatestProject = $location->kandangs->sortByDesc('latest_period')->first();
            $latestProject            = $kandangWithLatestProject->latest_project;
            $period                   = $latestProject->period;

            if (isset($input['period'])) {
                $period        = $input['period'];
                $latestProject = $location->kandangs->where('project.period', $period)->first();
            }

            $detail = (object) [
                'location_id'    => $location->location_id,
                'location'       => $location->name,
                'period'         => $project->period,
                'product'        => $project->product_category->name,
                'doc'            => $project->project_chick_in->first()->total_chickin ?? 0,
                'farm_type'      => $project->farm_type,
                'closing_date'   => 'dummy',
                'project_status' => $project->project_status,
                'kandang_name'   => $project->kandang->name,
                'chickin_date'   => $project->project_chick_in->first()->chickin_date ?? null,
                'ppl_ts'         => $project->kandang->user->name,
                'approval_date'  => $project->approval_date,
                // 'sapronak' => $this->sapronak(app(Request::class), $location, $project),
                'perhitungan_sapronak' => $this->perhitunganSapronak(app(Request::class), $location, $project),
                // 'stock_availability_usage' => StockAvailabilityUsage::with(['recording', 'recording.project'])
                // ->whereHas('recording.project', function($query) use($project){
                //     $query->where('project_id', $project->project_id);
                // })->get(),
                // 'stock_availability' => StockAvailability::with(['recording_stock', 'recording_stock.recording'])
                // ->whereHas('recording_stock.recording', function($query) use ($project){
                //     $query->where('project_id', $project->project_id);
                // })
                // ->get(),
                // 'purchase' => Purchase::with(['warehouse.kandang.project', 'purchase_item', 'purchase_item.product'])
                // ->whereHas('warehouse.kandang.project', function($query) use ($project){
                //     $query->where('project_id', $project->project_id);
                // })
                // ->get()
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

    // ! 80% | TINGGAL DATA DARI RECORDING
    public function sapronak(Request $req, Location $location, Project $project)
    {
        try {
            $period = intval($req->query('period') ?? $project->period);

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
                        'transaksi'    => 'Mutasi',
                        'produk'       => $si->product->name,
                        'gudang_asal'  => $si->origin->name,
                        'qty'          => Parser::toLocale($si->transfer_qty).' '.$si->product->uom->name,
                        'notes'        => $si->notes,
                    ];
                });

            $purchaseItems = Purchase::with(['warehouse.kandang.project', 'purchase_item', 'purchase_item.product'])
                ->whereHas('warehouse.kandang.project', function($query) use ($project) {
                    $query->where('project_id', $project->project_id);
                })
                ->get()
                ->flatMap(function($p) {
                    return $p->purchase_item->map(function($item) use ($p) {
                        return [
                            'tanggal'      => Carbon::parse($p->created_at)->format('d-M-Y'),
                            'no_referensi' => $p->purchase_id,
                            'transaksi'    => 'Pembelian',
                            'produk'       => $item->product->name,
                            'gudang_asal'  => '-',
                            'qty'          => Parser::toLocale($item->qty).' '.$item->product->uom->name,
                            'notes'        => $p->notes,
                        ];
                    });
                });

            $sapronakMasuk = $sapronakMasuk->concat($purchaseItems);

            $sapronakKeluar = StockMovement::with(['product', 'product.uom', 'origin', 'origin.kandang.project', 'destination'])
                ->whereHas('origin.kandang.project', function($query) use ($project, $period) {
                    $query->where([
                        ['project_id', $project->project_id],
                        ['period', $period],
                    ]);
                })
                ->whereHas('origin', function($query) use ($location) {
                    $query->where('location_id', $location->location_id);
                })
                ->get()
                ->transform(function($so) {
                    return [
                        'tanggal'       => Carbon::parse($so->created_at)->format('d-M-Y'),
                        'no_referensi'  => $so->stock_movement_id,
                        'transaksi'     => 'Mutasi',
                        'produk'        => $so->product->name,
                        'gudang_tujuan' => $so->destination->name,
                        'qty'           => Parser::toLocale($so->transfer_qty).' '.$so->product->uom->name,
                        'notes'         => $so->notes,
                    ];
                });

            // $recordingItems = StockAvailabilityUsage::with(['recording', 'recording.project'])
            // ->whereHas('recording.project', function($query) use($project){
            //     $query->where('project_id', $project->project_id);
            // })
            // ->whereHas('recording')
            // ->get()
            // ->flatMap(function($s){
            //     return $s->recording->map(function($r) use($s){
            //         return $r;
            //     });
            // });

            // $sapronakKeluar = $sapronakKeluar->concat($recordingItems);

            $sapronak = [
                'sapronak_masuk'  => $sapronakMasuk,
                'sapronak_keluar' => $sapronakKeluar,
            ];

            return response()->json($sapronak);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ! PENDING | MENUNGGU DATA DARI RECORDING
    public function perhitunganSapronak(Request $req, Location $location, Project $project)
    {
        try {
            $period = intval($req->query('period') ?? $project->period);

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
                        'transaksi'    => 'Mutasi',
                        'produk'       => $si->product->name,
                        'gudang_asal'  => $si->origin->name,
                        'qty'          => Parser::toLocale($si->transfer_qty).' '.$si->product->uom->name,
                        'notes'        => $si->notes,
                    ];
                });

            $purchaseItems = Purchase::with(['warehouse.kandang.project', 'purchase_item', 'purchase_item.product'])
                ->whereHas('warehouse.kandang.project', function($query) use ($project) {
                    $query->where('project_id', $project->project_id);
                })
                ->whereHas('purchase_item.product', function($query) {
                    $query->where('name', '');
                })
                ->get()
                ->flatMap(function($p) {
                    return $p->purchase_item->map(function($item) use ($p) {
                        return [
                            'tanggal'      => Carbon::parse($p->created_at)->format('d-M-Y'),
                            'no_referensi' => $p->purchase_id,
                            'transaksi'    => 'Pembelian',
                            'produk'       => $item->product->name,
                            'gudang_asal'  => '-',
                            'qty'          => Parser::toLocale($item->qty).' '.$item->product->uom->name,
                            'notes'        => $p->notes,
                        ];
                    });
                });

            $sapronakMasuk = $sapronakMasuk->concat($purchaseItems);

            return $sapronakMasuk;
            // return response()->json($);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function penjualan(Request $req, Location $location, Project $project)
    {
        try {
            $period = intval($req->query('period') ?? $project->period);

            $marketings = Marketing::with([
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
                ->get()
                ->transform(function($m) use ($project) {
                    $tanggal     = $m->realized_at ? Carbon::parse($m->realized_at)->format('d-M-Y') : '-';
                    $chickinDate = Carbon::parse($project->project_chick_in->first()->chickin_date);
                    $kandangs    = $m->marketing_products->map(function($mp) {
                        return $mp->warehouse->kandang->name;
                    })->unique()->values()->toArray();
                    $umur = $m->realized_at ? $chickinDate->diffInDays(Carbon::parse($m->realized_at)) : '-';

                    return [
                        'tanggal'            => $tanggal,
                        'umur'               => $umur,
                        'id_marketing'       => $m->id_marketing,
                        'customer'           => $m->customer->name,
                        'jumlah_ekor'        => $m->marketing_products->sum('qty'),
                        'jumlah_kg'          => $m->marketing_products->sum('weight_total'),
                        'harga'              => Parser::toLocale($m->marketing_products->sum('price')),
                        'cn'                 => 'dummy',
                        'total'              => Parser::toLocale($m->grand_total),
                        'kandangs'           => $kandangs,
                        'payment_status'     => Constants::MARKETING_PAYMENT_STATUS[$m->payment_status],
                        'marketing_products' => $m->marketing_products->map(fn ($mp) => [
                            'kandang'      => $mp->warehouse->kandang->name,
                            'nama_produk'  => $mp->product->name,
                            'harga_satuan' => $mp->price,
                            'bobot_avg'    => $mp->weight_avg,
                            'uom'          => $mp->uom->name,
                            'qty'          => $mp->qty,
                            'total_bobot'  => $mp->weight_total,
                        ]),
                        'marketing_addit_prices' => $m->marketing_addit_prices->map(fn ($ma) => [
                            'item'  => $ma->item,
                            'price' => $ma->price,
                        ]),
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
                            'price'   => Parser::toLocale($mp->price),
                            'total'   => $mp->qty != 0 ? Parser::toLocale($mp->price * $mp->qty) : Parser::toLocale($mp->price),
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
                            'id_marketing' => $d->marketing->id_marketing,
                            'supplier'     => $d->supplier->name,
                            'delivery_fee' => $d->delivery_fee,
                        ];
                    });
                });

            return response()->json($marketingDeliveries);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function dataProduksi($projectId)
    {
        try {
            $project = Project::find($projectId);

            $marketings = Marketing::whereHas('marketing_products', function($mp) use ($projectId) {
                $mp->where('project_id', $projectId);
            })->with(['marketing_delivery_vehicles.supplier'])->get();

            if ($marketings->isEmpty()) {
                return null;
            }

            $populasiAwal = $project->project_chick_in->first()->total_chickin;
            $culling      = 0;

            $dataProduksi = (object) [
                'populasi_awal'  => $populasiAwal,
                'claim_culling'  => $culling,
                'populasi_akhir' => $populasiAwal - $culling,
                // 'pakan_terkirim' =>
                // 'pakan_terpakai' =>
                // 'pakan_per_ekor' =>

                // 'penjualan_kg' =>
                // 'penjualan_ekor' =>
                // 'bobot_avg' =>
                // 'selling_price_avg' =>

                // 'deplesi' =>
                // 'umur' =>
                // 'mortalitas_act' =>
                // 'deff_mortalitas' =>
                // 'fcr_act' =>
                // 'deff_fcr' =>
                // 'adg' =>
                // 'ip' =>
            ];

            return $dataProduksi;
        } catch (\Exception $e) {
            return $e;
        }
    }
}
