<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Location;
use App\Models\Project\Project;
use Illuminate\Http\Request;

class ReportLocationController extends Controller
{
    public function index(Request $req)
    {
        try {
            $company = Company::where('alias', strtoupper($req->query('company')))
                ->select(['company_id', 'name'])
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

            return view('report.mbu.index', $param);
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
            $companyId = Company::where('alias', strtoupper($req->query('company')))->pluck('company_id')->first();

            if (empty($companyId)) {
                throw new \Exception('Invalid company');
            }

            $page   = $req->query('page');
            $detail = $project;

            switch (strtolower($page)) {
                case 'sapronak':
                    return $this->sapronak($detail);
                case 'perhitungan-sapronak':
                    return $this->perhitunganSapronak($detail);
                case 'penjualan':
                    return $this->penjualan($detail);
                case 'overhead':
                    return $this->overhead($detail);
                case 'hpp-ekspedisi':
                    return $this->hppEkspedisi($detail);
                case 'data-produksi':
                    return $this->dataProduksi($detail);
                case 'keuangan':
                    return $this->keuangan($detail);
                default:
                    return $this->sapronak($detail);
            }
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function sapronak($detail)
    {
        try {
            $param = [
                'title' => 'Laporan > MBU',
            ];

            return view('report.mbu.index', $param);
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
            $param = [
                'title' => 'Laporan > Manbu',
            ];

            return view('report.manbu.index', $param);
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
            $param = [
                'title' => 'Laporan > LTI',
            ];

            return view('report.lti.index', $param);
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
            $param = [
                'title' => 'Laporan > Detail Laporan Project',
            ];

            return view('report.mbu.detail', $param);
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
            $param = [
                'title' => 'Laporan > Detail Laporan Project',
            ];

            return view('report.mbu.detail', $param);
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
            $param = [
                'title' => 'Laporan > Detail Laporan Project',
            ];

            return view('report.mbu.detail', $param);
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
            $param = [
                'title' => 'Laporan > Detail Laporan Project',
            ];

            return view('report.mbu.detail', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
}
