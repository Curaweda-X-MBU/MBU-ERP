<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\DataMaster\Location;
use App\Models\Project\Project;

class ReportKandangController extends Controller
{
    public function detail(Location $location, Project $project)
    {
        try {
            $param = [
                'title' => 'Laporan > MBU',
                'data'  => collect(),
            ];

            return view('report.mbu.kandang', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
}
