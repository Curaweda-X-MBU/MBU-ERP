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

    public function detailLokasiMbu()
    {
        try {
            $param = [
                'title' => 'Laporan > Detail Laporan Project | Lokasi Pandeglang',
            ];

            return view('report.mbu.detail', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function detailLokasiManbu()
    {
        try {
            $param = [
                'title' => 'Laporan > Detail Laporan Project | Lokasi Pandeglang',
            ];

            return view('report.manbu.detail', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function detailLokasiLti()
    {
        try {
            $param = [
                'title' => 'Laporan > Detail Laporan Project | Lokasi Pandeglang',
            ];

            return view('report.lti.detail', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function detailKandangMbu()
    {
        try {
            $param = [
                'title' => 'Laporan > Detail Laporan Project | Lokasi Pandeglang | Pandeglang 1',
            ];

            return view('report.mbu.kandang', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function detailKandangManbu()
    {
        try {
            $param = [
                'title' => 'Laporan > Detail Laporan Project | Lokasi Pandeglang | Pandeglang 1',
            ];

            return view('report.manbu.kandang', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function detailKandangLti()
    {
        try {
            $param = [
                'title' => 'Laporan > Detail Laporan Project | Lokasi Pandeglang | Pandeglang 1',
            ];

            return view('report.lti.kandang', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
}
