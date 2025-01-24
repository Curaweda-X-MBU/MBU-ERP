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

    public function detail(Request $req, Project $project)
    {
        try {
            $company = Company::where('alias', strtoupper($req->query('company')))
                ->select(['company_id', 'name', 'alias'])
                ->first();

            if (empty($company)) {
                throw new \Exception('Invalid company');
            }

            $input  = $req->all();
            $period = $input['period'] ?? $project->period;
            $proj   = $project->where([
                ['project_id', '=', $project->project_id],
                ['period', '=', $period],
            ])->first();

            $detail = (object) [
                'project_id' => $proj->project_id,
                'location'   => $proj->kandang->location->name,
                'period'     => $proj->period,
                'product'    => $proj->product_category->name,
                'doc'        => $proj->project_chick_in->first()->total_chickin ?? 0,
                'farm_type'  => $proj->farm_type,
                // 'closing_date' => $proj,
                'project_status' => $proj->project_status,
                'active_kandang' => count($proj->kandang->location->kandangs->where('project_status', 1)),
                'start_date'     => $proj->created_at->format('d-M-Y'),
                'approval_date'  => $proj->approval_date,
                // 'payment_status' => $proj,
                // 'closing_status' => $proj,
                'kandangs' => $proj->kandang->location->kandangs->map(fn ($k) => (object) [
                    'kandang_id' => $k->kandang_id,
                    'name'       => $k->name,
                    'is_active'  => $k->project_status, // 0: not_active, 1: active
                ]),
            ];

            $param = [
                'title'  => 'Laporan > Detail',
                'detail' => $detail,
            ];

            return $this->checkAccess($company, $param, 'detail');
        } catch (\Exception $e) {
            dd($e->getMessage());
            // return redirect()
            //     ->back()
            //     ->with('error', $e->getMessage())
            //     ->withInput();
        }
    }

    public function sapronak($detail)
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

    public function perhitunganSapronak($detail)
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

    public function penjualan($detail)
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

    public function overhead($detail)
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

    public function hppEkspedisi($detail)
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

    public function dataProduksi($detail)
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

    public function keuangan($detail)
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
