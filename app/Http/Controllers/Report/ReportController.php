<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function indexMbu()
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

    public function indexManbu()
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

    public function indexLti()
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
}
