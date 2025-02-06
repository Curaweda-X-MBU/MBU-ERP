<?php

namespace App\Http\Controllers\Marketing;

use App\Constants;
use App\Helpers\FileHelper;
use App\Helpers\Parser;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Bank;
use App\Models\Marketing\Marketing;
use App\Models\Marketing\MarketingPayment;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

class ListPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Marketing $marketing)
    {
        try {
            if (Constants::MARKETING_STATUS[$marketing->marketing_status] !== 'Final' && Constants::MARKETING_STATUS[$marketing->marketing_status] !== 'Realisasi') {
                throw new \Exception('Status Penjualan belum final');
            }

            $data  = $marketing->load(['customer', 'company', 'marketing_payments', 'marketing_addit_prices']);
            $param = [
                'title' => 'Penjualan > Payment',
                'data'  => $data,
            ];

            return view('marketing.list.payment.index', $param);
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
                    'payment_nominal'    => Parser::parseLocale($input['payment_nominal']),
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

    public function batch(Request $req)
    {
        try {
            if ($req->hasFile('payment_csv')) {
                $file = $req->file('payment_csv');

                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension    = 'csv';
                $tempFileName = $originalName.'.'.$extension;
                $tempPath     = $file->storeAs('tmp', $tempFileName, 'local');
                $rows         = SimpleExcelReader::create(storage_path('app/'.$tempPath))
                    ->getRows()
                    ->toArray();

                $doNumbers = array_map('strtoupper', array_column($rows, 'do_number'));

                $marketings = Marketing::with(['marketing_payments', 'marketing_return.marketing_return_payments'])
                    ->whereNull('marketing_return_id')
                    ->whereIn('id_marketing', $doNumbers)
                    ->where('marketing_status', '>=', 3)
                    ->get(['marketing_id', 'id_marketing', 'grand_total'])
                    ->keyBy('id_marketing');

                // === SERVER-SIDE RENDERING ===
                // == GENERAL VALIDATION ==
                // Sort csv data
                usort($rows, function($a, $b) {
                    $numA = (int) filter_var($a['do_number'], FILTER_SANITIZE_NUMBER_INT);
                    $numB = (int) filter_var($b['do_number'], FILTER_SANITIZE_NUMBER_INT);

                    return $numA <=> $numB;
                });

                // Validate do_number
                $idMarketingValues = $marketings->keys()->toArray();

                $foundRows    = [];
                $notFoundRows = [];

                $validPayments = ['transfer', 'cash', 'card', 'cheque'];

                $banks = Bank::get()->map(fn ($bank) => [
                    'bank_id'        => $bank->bank_id,
                    'account_number' => $bank->account_number,
                    'text'           => $bank->alias.' - '.$bank->account_number.' - '.$bank->owner,
                ])->keyBy('bank_id')->toArray();
                $validBankAccounts    = Bank::pluck('account_number', 'bank_id')->toArray();
                $validBankAccountsSet = array_flip($validBankAccounts);

                function validateDateFormat($date)
                {
                    $format = 'd-M-Y';
                    $d      = DateTime::createFromFormat($format, $date);

                    return $d && $d->format($format) === $date;
                }

                foreach ($rows as $row) {
                    if (in_array($row['do_number'], $idMarketingValues)) {
                        // == COLUMNS VALIDATION ==
                        $row['has_invalid'] = false;

                        // Payment method
                        if (! empty($row['payment_method'])) {
                            if (! in_array(strtolower($row['payment_method']), $validPayments)) {
                                $row['payment_method_invalid'] = 'Metode ('.$row['payment_method'].') tidak valid.';
                                $row['has_invalid']            = true;
                            }
                        } else {
                            $row['payment_method_invalid'] = 'Metode pembayaran tidak boleh kosong.';
                            $row['has_invalid']            = true;
                        }

                        // Bank account
                        if (! empty($row['bank_account'])) {
                            $bankId = @$validBankAccountsSet[$row['bank_account']];
                            if (! isset($bankId)) {
                                $row['bank_account_invalid'] = 'Rekening ('.$row['bank_account'].') tidak ditemukan.';
                                $row['has_invalid']          = true;
                            } else {
                                $row['bank_id'] = $bankId;
                            }
                        }

                        // Date format
                        if (! empty($row['payment_date'])) {
                            if (! validateDateFormat($row['payment_date'])) {
                                $row['payment_date_invalid'] = 'Format tanggal ('.$row['payment_date'].') salah.';
                            }
                        } else {
                            $row['payment_date_invalid'] = 'Tanggal tidak boleh kosong.';
                        }

                        // Assign not_paid marketing informations
                        $idMarketing = strtoupper($row['do_number']);
                        if (isset($marketings[$idMarketing])) {
                            $row['marketing_id'] = $marketings[$idMarketing]->marketing_id;
                            $row['not_paid']     = $marketings[$idMarketing]->not_paid;
                            if ($row['not_paid'] - $row['payment_nominal'] < 0) {
                                $row['has_invalid'] = true;
                            }
                        }

                        $foundRows[] = $row;
                    } else {
                        $notFoundRows[] = [
                            'id_marketing' => $row['do_number'],
                            'message'      => $row['do_number'].' tidak ditemukan.',
                        ];
                    }
                }

                $param = [
                    // 'marketings' => $marketings->toArray(),
                    'payments'   => $foundRows,
                    'not_founds' => $notFoundRows,
                    'banks'      => $banks,
                ];

                Storage::disk('local')->delete($tempPath);

                return view('marketing.list.payment.batch', array_merge(['title' => 'Penjualan > Payment > Batch Upload'], $param));
            }

            throw new \Exception('Tidak ada data. Tolong upload ulang file csv.');
        } catch (\Exception $e) {
            return redirect()->route('marketing.list.index')->with('error', $e->getMessage())->withInput();
        }
    }

    public function batchAdd(Request $req)
    {
        try {
            $count = DB::transaction(function() use ($req) {
                $paymentBatches = ($req->all('payment_batch_upload')['payment_batch_upload']);
                $arrPayments    = [];
                $processedCount = 0;

                foreach ($paymentBatches as $key => $value) {
                    // skip if no marketing_id
                    if (empty($value['marketing_id']) || $value['marketing_id'] === '') {
                        continue;
                    }

                    $docPath = '';
                    if (isset($value['document_path'])) {
                        $docUrl = FileHelper::upload($value['document_path'], Constants::MARKETING_PAYMENT_DOC_PATH);
                        if (! $docUrl['status']) {
                            return redirect()->back()->with('error', $docUrl['message'].' '.$value['document_path'])->withInput();
                        }
                        $docPath = $docUrl['url'];
                    }

                    $paymentNominal = Parser::parseLocale($value['payment_nominal_mask']);

                    $arrPayments[] = [
                        'marketing_id'       => $value['marketing_id'],
                        'document_path'      => $docPath,
                        'transaction_number' => $value['transaction_number'],
                        'payment_reference'  => $value['payment_reference'],
                        'payment_method'     => $value['payment_method'],
                        'bank_id'            => $value['bank_id'] ?? null,
                        'payment_at'         => date('Y-m-d', strtotime($value['payment_at'])),
                        'payment_nominal'    => $paymentNominal,
                        'verify_status'      => array_search(
                            'Terverifikasi',
                            Constants::MARKETING_VERIFY_PAYMENT_STATUS
                        ),
                    ];

                    $marketing     = Marketing::find($value['marketing_id']);
                    $grandTotal    = $marketing->grand_total;
                    $totalIsPaid   = $marketing->is_paid;
                    $totalPayments = $totalIsPaid + $paymentNominal;

                    $paymentStatus = '';

                    if ($grandTotal < $totalPayments) {
                        $paymentStatus = 'Dibayar Lebih';
                    } elseif ($grandTotal == $totalPayments) {
                        $paymentStatus = 'Dibayar Penuh';
                    } elseif ($grandTotal > $totalPayments && $totalPayments > 0) {
                        $paymentStatus = 'Dibayar Sebagian';
                    } else {
                        $paymentStatus = 'Tempo';
                    }

                    $marketing->update([
                        'payment_status' => array_search($paymentStatus, Constants::MARKETING_PAYMENT_STATUS),
                    ]);

                    $processedCount += 1;
                }

                MarketingPayment::insert($arrPayments);

                return $processedCount;
            });

            $success = ['success' => "Sebanyak {$count} Data Berhasil diupload"];

            return redirect()->route('marketing.list.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->route('marketing.list.index')->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function detail(MarketingPayment $payment)
    {
        try {
            $data = $payment->load(['bank']);

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

                    if ($grandTotal == $totalPayments) {
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

    /**
     * Remove the specified resource from storage.
     */
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
                } elseif ($grandTotal == $totalPayments) {
                    $marketing->update([
                        'payment_status' => array_search('Dibayar Penuh', Constants::MARKETING_PAYMENT_STATUS),
                    ]);
                } elseif ($grandTotal > $totalPayments && $totalPayments > 0) {
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

                if ($grandTotal == $totalPayments) {
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

            dd($e);

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
