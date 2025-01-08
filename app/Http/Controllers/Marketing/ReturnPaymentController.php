<?php

namespace App\Http\Controllers\Marketing;

use App\Constants;
use App\Helpers\FileHelper;
use App\Helpers\Parser;
use App\Http\Controllers\Controller;
use App\Models\Marketing\Marketing;
use App\Models\Marketing\MarketingReturnPayment;
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
            if (Constants::MARKETING_RETURN_STATUS[$marketing->marketing_return->return_status] !== 'Disetujui') {
                throw new \Exception('Status Retur belum disetujui');
            }

            $data = $marketing->load(['customer', 'company', 'marketing_payments', 'marketing_addit_prices', 'marketing_return']);

            $data->return_sub_total = (($marketing->marketing_return->total_return
                - $marketing->marketing_addit_prices->sum('price'))
                + $marketing->discount)
                / (1 + ($marketing->tax / 100));

            $payments = $marketing->marketing_return->marketing_return_payments;

            $param = [
                'title'    => 'Penjualan > Retur > Payment',
                'data'     => $data,
                'payments' => $payments,
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
                    $docUrl = FileHelper::upload($input['document_path'], Constants::MARKETING_RETURN_PAYMENT_DOC_PATH);
                    if (! $docUrl['status']) {
                        return redirect()->back()->with('error', $docUrl['message'].' '.$input['document_path'])->withInput();
                    }
                    $docPath = $docUrl['url'];
                }

                MarketingReturnPayment::create([
                    'marketing_return_id' => $marketing->marketing_return_id,
                    'payment_method'      => $input['payment_method'],
                    'bank_id'             => $input['bank_id']           ?? null,
                    'recipient_bank_id'   => $input['recipient_bank_id'] ?? null,
                    'payment_reference'   => $input['payment_reference'],
                    'transaction_number'  => $input['transaction_number'],
                    'payment_nominal'     => Parser::parseLocale($input['payment_nominal']),
                    'bank_admin_fees'     => Parser::parseLocale($input['bank_admin_fees']),
                    'payment_at'          => date('Y-m-d', strtotime($input['payment_at'])),
                    'document_path'       => $docPath,
                    'notes'               => $input['notes'],
                    'verify_status'       => array_search(
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
    public function detail(MarketingReturnPayment $payment)
    {
        try {
            $data = $payment->load(['bank', 'recipient_bank']);

            return $data;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $req, MarketingReturnPayment $payment)
    {
        try {
            $data = $payment->load(['bank', 'recipient_bank']);

            if ($req->isMethod('post')) {
                DB::transaction(function() use ($req, $payment) {
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

                    $payment->update([
                        'payment_method'     => $input['payment_method'],
                        'bank_id'            => $input['bank_id']           ?? null,
                        'recipient_bank_id'  => $input['recipient_bank_id'] ?? null,
                        'payment_reference'  => $input['payment_reference'],
                        'transaction_number' => $input['transaction_number'],
                        'payment_nominal'    => Parser::parseLocale($input['payment_nominal']),
                        'bank_admin_fees'    => Parser::parseLocale($input['bank_admin_fees']),
                        'payment_at'         => date('Y-m-d', strtotime($input['payment_at'])),
                        'document_path'      => $docPath,
                        'notes'              => $input['notes'],
                    ]);
                });
                $success = ['success' => 'Data Berhasil diubah'];

                return redirect()
                    ->back()
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
    public function delete(MarketingReturnPayment $payment)
    {
        try {
            $payment->delete();
            $success = ['success' => 'Data Berhasil dihapus'];

            return redirect()->back()->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        }
    }

    public function approve(Request $req, MarketingReturnPayment $payment)
    {
        DB::beginTransaction();
        try {
            $input = $req->all();

            $success       = [];
            $verify_status = '';
            $approved_at   = null;

            if ($input['is_approved'] == 1) {
                $success       = ['success' => 'Pembayaran berhasil disetujui'];
                $verify_status = array_search('Terverifikasi', Constants::MARKETING_VERIFY_PAYMENT_STATUS);
                $approved_at   = now()->format('Y-m-d H:i:s');
            } else {
                $success       = ['success' => 'Pembayaran berhasil ditolak'];
                $verify_status = array_search('Ditolak', Constants::MARKETING_VERIFY_PAYMENT_STATUS);
            }

            $payment->update([
                'is_approved'    => $input['is_approved'],
                'approver_id'    => Auth::id(),
                'approval_notes' => $input['approval_notes'],
                'approved_at'    => $approved_at,
                'verify_status'  => $verify_status,
            ]);

            DB::commit();

            return redirect()->back()->with($success);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
