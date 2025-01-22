<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;

class ReportKandangController extends Controller
{
    public function sapronak()
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

    public function perhitunganSapronak()
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

    public function penjualan()
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

    public function overhead()
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

    public function hppEkspedisi()
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

    public function dataProduksi()
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

    public function keuangan()
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
