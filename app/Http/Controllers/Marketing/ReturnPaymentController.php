<?php

namespace App\Http\Controllers\Marketing;

use App\Constants;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Models\Marketing\Marketing;
use App\Models\Marketing\MarketingPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Marketing $marketing)
    {
        try {
            $data  = $marketing->load(['customer', 'company', 'marketing_payments']);
            $param = [
                'title' => 'Penjualan > Payment',
                'data'  => $data,
            ];

            return view('marketing.return.payment.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function add(Request $req, Marketing $marketing)
    {
        try {
            DB::transaction(function() use ($req, $marketing) {
                $input = $req->all();

                $docPath = '';
                if ($req->hasFile('document_path')) {
                    $docUrl = FileHelper::upload($input['document_path'], Constants::MARKETING_PAYMENT_DOC_PATH);
                    if (! $docUrl['status']) {
                        return redirect()->back()->with('error', $docUrl['message'].' '.$input['document_path'])->withInput();
                    }
                    $docPath = $docUrl['url'];
                }

                MarketingPayment::create([
                    'marketing_id'       => $marketing->marketing_id,
                    'payment_method'     => $input['payment_method'],
                    'bank_id'            => $input['bank_id'] ?? null,
                    'payment_reference'  => $input['payment_reference'],
                    'transaction_number' => $input['transaction_number'],
                    'payment_nominal'    => str_replace(',', '', $input['payment_nominal'] ?? 0),
                    'payment_at'         => date('Y-m-d', strtotime($input['payment_at'])),
                    'document_path'      => $docPath,
                    'notes'              => $input['notes'],
                    'verify_status'      => array_search(
                        'Diajukan',
                        Constants::MARKETING_VERIFY_PAYMENT_STATUS
                    ),
                ]);
            });
            $success = ['success' => 'Data Berhasil disimpan'];

            return redirect()
                ->back()
                ->with($success);
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
            $data = $marketing->load([
                'company',
                'customer',
                'sales',
                'marketing_products.warehouse',
                'marketing_products.product',
                'marketing_products.uom',
                'marketing_addit_prices',
                'marketing_delivery_vehicles.uom',
                'marketing_delivery_vehicles.sender',
            ]);

            return $data;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $req, Marketing $marketing)
    {
        try {
            $data = $marketing->load(['approver', 'marketing', 'bank']);

            if ($req->isMethod('post')) {
                DB::transaction(function() use ($req, $data) {
                    $input = $req->all();

                    $existingDoc = $data->document_path ?? null;
                    $docPath     = '';
                    if ($req->hasFile('document_path')) {
                        if ($existingDoc) {
                            FileHelper::delete($existingDoc);
                        }

                        $docUrl = FileHelper::upload($input['document_path'], constants::MARKETING_PAYMENT_DOC_PATH);
                        if (! $docUrl['status']) {
                            return redirect()->back()->with('error', $docUrl['message'].' '.$input['document_path'])->withInput();
                        }
                        $docPath = $docUrl['url'];
                    } else {
                        $docPath = $existingDoc;
                    }

                    MarketingPayment::create([
                        'payment_method'     => $input['payment_method'],
                        'bank_id'            => $input['bank_id'],
                        'payment_reference'  => $input['payment_reference'],
                        'transaction_number' => $input['transaction_number'],
                        'payment_nominal'    => str_replace(',', '', $input['payment_nominal'] ?? 0),
                        'payment_at'         => date('Y-m-d', strtotime($input['payment_at'])),
                        'document_path'      => $docPath,
                        'notes'              => $input['notes'],
                    ]);
                });
                $success = ['success' => 'Data Berhasil diubah'];

                return redirect()
                    ->route('marketing.list.payment.index')
                    ->with($success);
            }

            return $data;
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

            return $success;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        }
    }

    public function approve(Request $req, MarketingPayment $marketingPayment)
    {
        try {
            $input   = $req->all();
            $payment = $marketingPayment->get();

            $success = [];

            if ($input['is_approved'] === 0) {
                $payment->update([
                    'is_approved'    => array_search('Tidak Disetujui', Constants::MARKETING_APPROVAL),
                    'approver_id'    => Auth::id(),
                    'approval_notes' => $input['approval_notes'],
                ]);

                $success = ['success' => 'Payment berhasil ditolak'];
            } else {
                $payment->update([
                    'is_approved' => array_search('Disetujui', Constants::MARKETING_APPROVAL),
                    'approver_id' => Auth::id(),
                    'approved_at' => date('Y-m-d H:i:s'),
                ]);

                $success = ['success' => 'Payment berhasil disetujui'];
            }

            return redirect()->route('marketing.list.payment.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
