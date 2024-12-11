<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\MarketingPayment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private const VALIDATION_RULES = [];

    private const VALIDATION_MESSAGES = [];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = MarketingPayment::with(['approver', 'bank', 'marketing'])->get();
            $param = [
                'title' => 'Penjualan > Payment',
                'data' => $data,
            ];

            return view('marketing.payment.index', $param);
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
                'title' => 'Penjualan > Payment > Tambah',
            ];

            return view('marketing.payment.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function detail(MarketingPayment $payment)
    {
        try {
            $data = $payment->with(['approver', 'marketing', 'bank'])->get();
            $param = [
                'title' => 'Penjualan > Payment > Detail',
                'data' => $data,
            ];

            return view('marketing.payment.detail', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $req, MarketingPayment $payment)
    {
        try {
            $data = $payment->with(['approver', 'marketing', 'bank']);
            $param = [
                'title' => 'Penjualan > Payment > Edit',
                'data' => $data,
            ];

            return view('marketing.payment.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(MarketingPayment $payment)
    {
        try {
            $payment->delete();
            $success = ['success' => 'Data Berhasil dihapus'];

            return redirect()->route('marketing.list.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function searchPayment(Request $req)
    {
        //
    }
}
