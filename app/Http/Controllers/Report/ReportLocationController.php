<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Location;
use App\Models\Project\Project;
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
                ->whereHas('kandangs.project')
                ->select(['location_id', 'name'])
                ->get()
                ->map(function($loc) {
                    $project             = $loc->kandangs->sortByDesc('project.period')->first()->project->first();
                    $loc->project_id     = $project->project_id;
                    $loc->period         = $project->period;
                    $loc->farm_type      = $project->farm_type;
                    $loc->count_kandang  = count($loc->kandangs);
                    $loc->project_status = $project->project_status;

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
                'project_status' => $latestProject->project_status,
                'active_kandang' => count($kandangs->where('project_status', 1)),
                'chickin_date'   => $latestProject->created_at, // ? NEED FIX
                'approval_date'  => $latestProject->approval_date,
                // 'payment_status' => $proj,
                // 'closing_status' => $proj,
                'kandangs' => $kandangs,
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

    public function penjualan($projectId)
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

    public function overhead($projectId)
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
