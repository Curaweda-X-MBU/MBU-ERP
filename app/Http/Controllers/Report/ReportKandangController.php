<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Location;
use App\Models\Marketing\Marketing;
use App\Models\Project\Project;
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
                $latestProject = $location->kandangs->where('project.period', $input['period'])->first();
            }

            $detail = (object) [
                'location_id' => $location->location_id,
                'location'    => $location->name,
                'period'      => $project->period,
                'product'     => $project->product_category->name,
                'doc'         => $project->project_chick_in->first()->total_chickin ?? 0,
                'farm_type'   => $project->farm_type,
                // 'closing_date' => $proj,
                'project_status' => $project->project_status,
                'kandang_name'   => $project->kandang->name,
                'chickin_date'   => $project->created_at, // ? NEED FIX
                // 'ppl_ts'   => $project,
                'approval_date' => $project->approval_date,
                'ekspedisi'     => $this->hppEkspedisi($project->project_id),
                'penjualan'     => $this->penjualan($project->project_id),
                'data_produksi' => $this->dataProduksi($project->project_id),
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

    public function penjualan($projectId)
    {
        try {
            $marketings = Marketing::whereHas('marketing_products', function($mp) use ($projectId) {
                $mp->where('project_id', $projectId);
            })->with(['marketing_products', 'marketing_addit_prices'])->get();

            if ($marketings->isEmpty()) {
                return null;
            }

            return $marketings;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function hppEkspedisi($projectId)
    {
        try {
            $marketings = Marketing::whereHas('marketing_products', function($mp) use ($projectId) {
                $mp->where('project_id', $projectId);
            })->with(['marketing_delivery_vehicles.supplier'])->get();

            if ($marketings->isEmpty()) {
                return null;
            }

            $marketingDeliveries = $marketings->flatMap(function($m) {
                return $m->marketing_delivery_vehicles->map(function($d) {
                    return (object) [
                        'id_marketing' => $d->marketing->id_marketing,
                        'supplier'     => $d->supplier->name,
                        'delivery_fee' => $d->delivery_fee,
                    ];
                });
            });

            return $marketingDeliveries;
        } catch (\Exception $e) {
            return $e;
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
