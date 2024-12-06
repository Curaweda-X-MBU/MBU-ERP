<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\MarketingRetur;
use Illuminate\Http\Request;

class ReturController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $param = [
                'title' => 'Penjualan > Retur'
            ];
            return view('marketing.retur.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function add()
    {
        try {
            $param = [
                'title' => 'Penjualan > Retur > Tambah'
            ];
            return view('marketing.retur.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function detail(Marketing $marketing)
    {
        try {
            $param = [
                'title' => 'Penjualan > Retur > Detail'
            ];
            return view('marketing.retur.detail', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Marketing $marketing)
    {
        try {
            $param = [
                'title' => 'Penjualan > Retur > Edit'
            ];
            return view('marketing.retur.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Marketing $marketing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function searchMarketing(Marketing $marketing)
    {
        //
    }
}
