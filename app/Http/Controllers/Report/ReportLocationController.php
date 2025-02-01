<?php

namespace App\Http\Controllers\Report;

use App\Constants;
use App\Helpers\Parser;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Location;
use App\Models\Expense\Expense;
use App\Models\Marketing\Marketing;
use App\Models\Project\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $period                   = $latestProject->period;
            $projectStatus            = $location->kandangs->flatMap(function($k) use ($period) {
                return $k->project->where('period', $period);
            })->max('project_status');
            if (isset($input['period'])) {
                $period        = $input['period'];
                $latestProject = $location->kandangs->where('project.period', $input['period'])->first();
            }

            $kandangs = $location->kandangs->select(['kandang_id', 'name', 'project_status', 'latest_period', 'latest_project'])
                ->map(function($k) use ($input) {
                    $k['is_active']      = $k['project_status'];
                    $k['latest_period']  = isset($input['period']) ? $input['period'] : $k['latest_period'];
                    $k['latest_project'] = isset($input['period']) ? $k->projects->where('period', $input['period']) : ($k['latest_project'] ?? null);

                    return (object) $k;
                });

            $detail = (object) [
                'location_id' => $location->location_id,
                'location'    => $location->name,
                'period'      => $period,
                'product'     => $latestProject->product_category->name,
                'doc'         => $latestProject->project_chick_in->first()->total_chickin ?? 0,
                'farm_type'   => $latestProject->farm_type,
                // 'closing_date' => $proj,
                'project_status' => $projectStatus,
                'active_kandang' => count($kandangs->where('project_status', 1)),
                'chickin_date'   => $latestProject->project_chick_in->sortByDesc('chickin_date')->first()->chickin_date ?? null, // NOTE: NEED FIX
                'approval_date'  => $latestProject->approval_date,
                // 'payment_status' => $proj,
                // 'closing_status' => $proj,
                'kandangs' => $kandangs,
            ];

            // dd(
            //     Expense::with([
            //         'expense_main_prices:expense_item_id,expense_id,sub_category,qty,uom,price',
            //         'expense_addit_prices:expense_addit_price_id,expense_id,name,price',
            //         'expense_kandang.project.project_budget.nonstock:name',
            //     ])
            //         ->where([
            //             ['location_id', $location->location_id],
            //             ['expense_status', 2],
            //         ])
            //         ->whereIn('category', [1, 2])
            //         ->where(function($q) use ($period) {
            //             $q->where('category', 2)
            //                 ->orWhereHas('expense_kandang.project', function($p) use ($period) {
            //                     $p->where('period', $period);
            //                 });
            //         })->get()
            // );

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

    public function sapronak($projectId)
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

    public function perhitunganSapronak($projectId)
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
                ->map(function($m) {
                    $chickinDate = $m->marketing_products
                        ->first()
                        ->project
                        ->first()
                        ->project_chick_in
                        ->sortByDesc('chickin_date')
                        ->first()
                        ->chickin_date;

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

            $formatted_expenses = [
                'bop'  => [],
                'nbop' => [],
            ];

            $query = Expense::with([
                'expense_main_prices:expense_item_id,expense_id,sub_category,qty,uom,price',
                'expense_addit_prices:expense_addit_price_id,expense_id,name,price',
                'expense_kandang.project.project_budget.nonstock' => function($query) {
                    $query->select('id', 'project_budget_id', 'name'); // Load only necessary fields
                },
            ])
                ->where('location_id', $location->location_id)
                ->where('expense_status', 2)
                ->whereIn('category', [1, 2]);

            $query->where(function($q) use ($period) {
                $q->where('category', 2)
                    ->orWhereHas('expense_kandang.project', function($p) use ($period) {
                        $p->where('period', $period);
                    });
            });

            $expenses = $query->cursor();

            $bop_expenses  = [];
            $nbop_expenses = [];

            foreach ($expenses as $e) {
                $processed = collect($e->expense_main_prices
                    ->concat($e->expense_addit_prices)
                    ->transform(function($p) use ($e) {
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
                            'tanggal'           => Carbon::parse($p->expense->approved_at)->format('d-M-Y'),
                            'no_ref'            => '####', // dummy
                            'produk'            => $p->sub_category ?? "{$p->name} (Lainnya)",
                            'budget_qty'        => $budget->qty     ?? '-',
                            'budget_price'      => isset($budget->price) ? Parser::toLocale($budget->price) : '-',
                            'budget_total'      => isset($budget->total) ? Parser::toLocale($budget->total) : '-',
                            'realization_qty'   => ($p->total_qty ?? $p->qty) ?? '-',
                            'uom'               => $p->uom                    ?? '',
                            'realization_price' => Parser::toLocale($p->price),
                            'realization_total' => Parser::toLocale($p->total_price),
                            'price_per_qty'     => $p->qty ? Parser::toLocale($p->price / ($p->qty ?? 1)) : '-',
                        ];
                    }));

                if ($e->category == 1) {
                    $formatted_expenses['bop'] = array_merge($formatted_expenses['bop'], $processed->toArray());
                } else {
                    $formatted_expenses['nbop'] = array_merge($formatted_expenses['nbop'], $processed->toArray());
                }
            }

            $bop_expenses  = collect($formatted_expenses['bop']);
            $nbop_expenses = collect($formatted_expenses['nbop']);

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
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function hppEkspedisi($projectId)
    {
        try {
            $project = Project::find($projectId);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function dataProduksi($projectId)
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

    public function keuangan($projectId)
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
}
