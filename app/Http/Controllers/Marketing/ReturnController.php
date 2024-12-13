<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\MarketingReturn;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data  = MarketingReturn::with(['marketing'])->get();
            $param = [
                'title' => 'Penjualan > Retur',
                'data'  => $data,
            ];

            return view('marketing.return.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function add(Request $req)
    {
        try {
            $param = [
                'title' => 'Penjualan > Retur > Tambah',
            ];

            return view('marketing.return.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function detail(MarketingReturn $marketingReturn)
    {
        $data = $marketingReturn->with(['marketing'])->get();
        try {
            $param = [
                'title' => 'Penjualan > Retur > Detail',
                'data'  => $data,
            ];

            return view('marketing.return.detail', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $req, MarketingReturn $marketingReturn)
    {
        try {
            $data  = $marketingReturn->with(['marketing'])->get();
            $param = [
                'title' => 'Penjualan > Retur > Edit',
                'data'  => $data,
            ];

            return view('marketing.return.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(MarketingReturn $marketingReturn)
    {
        try {
            $marketingReturn->delete();

            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('marketing.list.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function searchMarketing(Request $req)
    {
        //
    }

    public function payment(MarketingReturn $marketingReturn)
    {
        $data = $marketingReturn->with(['marketing'])->get();
        try {
            $param = [
                'title' => 'Penjualan > Retur > Pembayaran Retur Penjualan',
                'data'  => $data,
            ];

            return view('marketing.return.payment', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
