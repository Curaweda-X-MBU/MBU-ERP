<?php

namespace App\Http\Controllers\Marketing;

use App\Constants;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Bank;
use App\Models\Marketing\Marketing;
use App\Models\Marketing\MarketingPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    private const VALIDATION_RULES = [
        'marketing_id'    => 'required',
        'payment_method'  => 'required',
        'bank_id'         => 'required',
        'payment_nominal' => 'required',
        'payment_at'      => 'required',
        'document_path'   => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
    ];

    private const VALIDATION_MESSAGES = [
        'marketing_id.required'    => 'ID Marketing tidak boleh kosong',
        'payment_method.required'  => 'Metode Pembayaran tidak boleh kosong',
        'bank_id.required'         => 'Akun Bank tidak boleh kosong',
        'payment_nominal.required' => 'Nominal Pembayaran tidak boleh kosong',
        'payment_at.required'      => 'Tanggal Bayar tidak boleh kosong',
        'document_path.file'       => 'Dokumen tidak valid',
        'document_path.mimes'      => 'Dokumen hanya boleh pdf, jpeg, png, atau jpg',
        'document_path.max'        => 'Ukuran file tidak boleh lebih dari 5MB',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data  = MarketingPayment::with(['approver', 'bank', 'marketing'])->get();
            $param = [
                'title' => 'Penjualan > Payment',
                'data'  => $data,
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
            if ($req->isMethod('post')) {
                $input     = $req->all();
                $validator = Validator::make($input, self::VALIDATION_RULES, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    if (isset($input['marketing_id'])) {
                        $marketing          = Marketing::with(['customer'])->find($input['marketing_id']);
                        $input['marketing'] = [
                            'id_marketing'  => $marketing->id_marketing,
                            'customer_name' => $marketing->customer->name,
                            'grand_total'   => $marketing->grand_total,
                        ];
                    }
                    if (isset($input['bank_id'])) {
                        $bank               = Bank::find($input['bank_id']);
                        $input['bank_name'] = `{$bank->alias} - {$bank->account_number} - {$bank->owner}`;
                    }

                    return redirect()
                        ->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                DB::transaction(function() use ($req) {
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
                        'marketing_id'       => $input['marketing_id'],
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
                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()
                    ->route('marketing.payment.index')
                    ->with($success);
            }
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

            return $data;
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

            if ($req->isMethod('post')) {
                $input     = $req->all();
                $validator = Validator::make($input, self::VALIDATION_RULES, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    if (isset($input['marketing_id'])) {
                        $marketing          = Marketing::with(['customer'])->find($input['marketing_id']);
                        $input['marketing'] = [
                            'id_marketing'  => $marketing->id_marketing,
                            'customer_name' => $marketing->customer->name,
                            'grand_total'   => $marketing->grand_total,
                        ];
                    }
                    if (isset($input['bank_id'])) {
                        $bank               = Bank::find($input['bank_id']);
                        $input['bank_name'] = `{$bank->alias} - {$bank->account_number} - {$bank->owner}`;
                    }

                    return redirect()
                        ->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

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
                    ->route('marketing.payment.index')
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

            return redirect()->route('marketing.payment.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
