<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Marketing;

class ListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $param = [
                'title' => 'Penjualan > List',
            ];

            return view('marketing.list.index', $param);
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
                'title' => 'Penjualan > Tambah',
            ];

            return view('marketing.list.add', $param);
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
                'title' => 'Penjualan > Detail',
            ];

            return view('marketing.list.detail', $param);
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
                'title' => 'Penjualan > Edit',
            ];

            return view('marketing.list.edit', $param);
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
     * Search the specified resource from storage.
     */
    public function realization(Marketing $marketing)
    {
        try {
            $param = [
                'title' => 'Penjualan > Realisasi',
            ];

            return view('marketing.list.realization', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Search the specified resource from storage.
     */
    public function payment(Marketing $marketing)
    {
        try {
            $param = [
                'title' => 'Penjualan > Pembayaran',
            ];

            return view('marketing.list.payment', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Search the specified resource from storage.
     */
    public function searchMarketing(Marketing $marketing)
    {
        //
    }
}
