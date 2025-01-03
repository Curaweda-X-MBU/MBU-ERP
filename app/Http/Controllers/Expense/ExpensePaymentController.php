<?php

namespace App\Http\Controllers\Expense;

use App\Constants;
use App\Helpers\FileHelper;
use App\Helpers\Parser;
use App\Http\Controllers\Controller;
use App\Models\Expense\Expense;
use App\Models\Expense\ExpensePayment;
use App\Models\Marketing\MarketingPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpensePaymentController extends Controller
{
    public function index(Expense $expense)
    {
        try {
            if (Constants::EXPENSE_STATUS[$expense->expense_status] !== 'Disetujui') {
                throw new \Exception('Status Biaya belum disetujui');
            }

            $data = $expense->load([
                'created_user',
                'location',
                'expense_main_prices',
                'expense_addit_prices',
                'expense_payments',
            ]);

            $param = [
                'title' => 'Biaya > Payment',
                'data'  => $data,
            ];

            return view('expense.list.payment.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req, Expense $expense)
    {
        try {
            DB::transaction(function() use ($req, $expense) {
                $input = $req->all();

                $docPath = '';
                if ($req->hasFile('document_path')) {
                    $docUrl = FileHelper::upload($input['document_path'], Constants::MARKETING_PAYMENT_DOC_PATH);
                    if (! $docUrl['status']) {
                        return redirect()->back()->with('error', $docUrl['message'].' '.$input['document_path'])->withInput();
                    }
                    $docPath = $docUrl['url'];
                }

                ExpensePayment::create([
                    'marketing_id'       => $expense->marketing_id,
                    'payment_method'     => $input['payment_method'],
                    'bank_id'            => $input['bank_id'] ?? null,
                    'payment_reference'  => $input['payment_reference'],
                    'transaction_number' => $input['transaction_number'],
                    'payment_nominal'    => Parser::parseLocale($input['payment_nominal']),
                    'payment_at'         => date('Y-m-d', strtotime($input['payment_at'])),
                    'document_path'      => $docPath,
                    'notes'              => $input['notes'],
                    'verify_status'      => 1,
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

    public function detail(ExpensePayment $payment)
    {
        try {
            $data = $payment->load(['bank']);

            return $data;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req, ExpensePayment $payment)
    {
        try {
            $data = $payment->load(['bank']);

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
                        'bank_id'            => $input['bank_id'],
                        'payment_reference'  => $input['payment_reference'],
                        'transaction_number' => $input['transaction_number'],
                        'payment_nominal'    => Parser::parseLocale($input['payment_nominal']),
                        'payment_at'         => date('Y-m-d', strtotime($input['payment_at'])),
                        'document_path'      => $docPath,
                        'notes'              => $input['notes'],
                    ]);

                    $grandTotal    = $payment->marketing->grand_total;
                    $totalPayments = $payment->marketing
                        ->marketing_payments
                        ->filter(fn ($p) => $p->verify_status == 2)
                        ->sum('payment_nominal');

                    if ($grandTotal === $totalPayments) {
                        $payment->marketing->update([
                            'payment_status' => array_search('Dibayar Penuh', Constants::MARKETING_PAYMENT_STATUS),
                        ]);
                    } elseif ($grandTotal < $totalPayments) {
                        $payment->marketing->update([
                            'payment_status' => array_search('Dibayar Lebih', Constants::MARKETING_PAYMENT_STATUS),
                        ]);
                    } else {
                        $payment->marketing->update([
                            'payment_status' => array_search('Dibayar Sebagian', Constants::MARKETING_PAYMENT_STATUS),
                        ]);
                    }
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

    public function delete(MarketingPayment $payment)
    {
        try {
            DB::transaction(function() use ($payment) {
                $payment->delete();

                $marketing     = $payment->marketing;
                $grandTotal    = $marketing->grand_total;
                $totalPayments = $marketing->marketing_payments
                    ->filter(fn ($p) => $p->verify_status == 2)
                    ->sum('payment_nominal');

                if ($grandTotal < $totalPayments) {
                    $marketing->update([
                        'payment_status' => array_search('Dibayar Lebih', Constants::MARKETING_PAYMENT_STATUS),
                    ]);
                } elseif ($grandTotal === $totalPayments) {
                    $marketing->update([
                        'payment_status' => array_search('Dibayar Penuh', Constants::MARKETING_PAYMENT_STATUS),
                    ]);
                } elseif ($grandTotal > $totalPayments) {
                    $marketing->update([
                        'payment_status' => array_search('Dibayar Sebagian', Constants::MARKETING_PAYMENT_STATUS),
                    ]);
                } else {
                    $marketing->update([
                        'payment_status' => array_search('Tempo', Constants::MARKETING_PAYMENT_STATUS),
                    ]);
                }
            });
            $success = ['success' => 'Data Berhasil dihapus'];

            return redirect()->back()->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        }
    }

    public function approve(Request $req, MarketingPayment $payment)
    {
        DB::beginTransaction();
        try {
            $input = $req->all();

            $success = [];

            if ($input['is_approved'] == 1) {
                $payment->update([
                    'is_approved'    => array_search('Disetujui', Constants::MARKETING_APPROVAL),
                    'approver_id'    => Auth::id(),
                    'approved_at'    => date('Y-m-d H:i:s'),
                    'approval_notes' => $input['approval_notes'],
                    'verify_status'  => array_search('Terverifikasi', Constants::MARKETING_VERIFY_PAYMENT_STATUS),
                ]);

                $grandTotal    = $payment->marketing->grand_total;
                $totalPayments = $payment->marketing
                    ->marketing_payments
                    ->filter(fn ($p) => $p->verify_status == 2)
                    ->sum('payment_nominal');

                if ($grandTotal === $totalPayments) {
                    $payment->marketing->update([
                        'payment_status' => array_search('Dibayar Penuh', Constants::MARKETING_PAYMENT_STATUS),
                    ]);
                } elseif ($grandTotal < $totalPayments) {
                    $payment->marketing->update([
                        'payment_status' => array_search('Dibayar Lebih', Constants::MARKETING_PAYMENT_STATUS),
                    ]);
                } else {
                    $payment->marketing->update([
                        'payment_status' => array_search('Dibayar Sebagian', Constants::MARKETING_PAYMENT_STATUS),
                    ]);
                }

                $success = ['success' => 'Pembayaran berhasil disetujui'];
            } else {
                $payment->update([
                    'is_approved'    => array_search('Tidak Disetujui', Constants::MARKETING_APPROVAL),
                    'approver_id'    => Auth::id(),
                    'approval_notes' => $input['approval_notes'],
                    'verify_status'  => array_search('Ditolak', Constants::MARKETING_VERIFY_PAYMENT_STATUS),
                ]);

                $success = ['success' => 'Pembayaran berhasil ditolak'];
            }

            DB::commit(); // Commit transaksi jika semua berhasil

            return redirect()->back()->with($success);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi kesalahan

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
