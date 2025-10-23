<?php

namespace App\Http\Controllers\Expense;

use App\Constants;
use App\Helpers\FileHelper;
use App\Helpers\Parser;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Bank;
use App\Models\Expense\Expense;
use App\Models\Expense\ExpenseDisburse;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

class ExpenseDisburseController extends Controller
{
    public function index(Expense $expense)
    {
        try {
            if ($expense->expense_status < array_search('Pencairan', Constants::EXPENSE_STATUS)) {
                throw new \Exception('Status Biaya belum disetujui oleh Finance');
            }

            $data = $expense->load([
                'created_user',
                'location',
                'expense_main_prices',
                'expense_addit_prices',
                'expense_disburses',
            ]);

            $param = [
                'title' => 'Biaya > Pencairan',
                'data'  => $data,
            ];

            return view('expense.list.disburse.index', $param);
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
                    $docUrl = FileHelper::upload($input['document_path'], Constants::EXPENSE_DISBURSE_DOC_PATH);
                    if (! $docUrl['status']) {
                        return redirect()->back()->with('error', $docUrl['message'].' '.$input['document_path'])->withInput();
                    }
                    $docPath = $docUrl['url'];
                }

                ExpenseDisburse::create([
                    'expense_id'         => $expense->expense_id,
                    'payment_method'     => $input['payment_method'],
                    'bank_id'            => $input['bank_id'] ?? null,
                    'payment_reference'  => $input['payment_reference'],
                    'transaction_number' => $input['transaction_number'],
                    'payment_nominal'    => Parser::parseLocale($input['payment_nominal']),
                    'payment_at'         => date('Y-m-d', strtotime($input['payment_at'])),
                    'disburse_docs'      => $docPath,
                    'notes'              => $input['notes'],
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

                $idExpense = array_map('strtoupper', array_column($rows, 'id_expense'));

                $expenses = Expense::with([
                    'expense_disburses',
                    'expense_main_prices:expense_id,price',
                    'expense_addit_prices:expense_id,price',
                ])
                    ->whereIn('id_expense', $idExpense)
                    ->where('expense_status', '=', 2)
                    ->get(['expense_id', 'id_expense'])
                    ->keyBy('id_expense');

                foreach ($expenses as $expense) {
                    $expense->grand_total = $expense->grand_total;
                }

                // === SERVER-SIDE RENDERING ===
                // == GENERAL VALIDATION ==
                // Sort csv data
                usort($rows, function($a, $b) {
                    $numA = (int) filter_var($a['id_expense'], FILTER_SANITIZE_NUMBER_INT);
                    $numB = (int) filter_var($b['id_expense'], FILTER_SANITIZE_NUMBER_INT);

                    return $numA <=> $numB;
                });

                // Validate do_number
                $idExpenseValues = $expenses->keys()->toArray();

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
                    if (in_array($row['id_expense'], $idExpenseValues)) {
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

                        // Assign not_paid expense informations
                        $idExpense = strtoupper($row['id_expense']);
                        if (isset($expenses[$idExpense])) {
                            $row['expense_id'] = $expenses[$idExpense]->expense_id;
                            $row['not_paid']   = $expenses[$idExpense]->not_paid;
                            if ($row['not_paid'] - $row['payment_nominal'] < 0) {
                                $row['has_invalid'] = true;
                            }
                        }

                        $foundRows[] = $row;
                    } else {
                        $notFoundRows[] = [
                            'id_expense' => $row['id_expense'],
                            'message'    => $row['id_expense'].' tidak ditemukan.',
                        ];
                    }
                }

                $param = [
                    // 'expenses' => $expenses->toArray(),
                    'payments'   => $foundRows,
                    'not_founds' => $notFoundRows,
                    'banks'      => $banks,
                ];

                Storage::disk('local')->delete($tempPath);

                return view('expense.list.disburse.batch', array_merge(['title' => 'Biaya > Pencairan > Batch Upload'], $param));
            }

            throw new \Exception('Tidak ada data. Tolong upload ulang file csv.');
        } catch (\Exception $e) {
            return redirect()->route('expense.list.index')->with('error', $e->getMessage())->withInput();
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
                    // skip if no expense_id
                    if (empty($value['expense_id']) || $value['expense_id'] === '') {
                        continue;
                    }

                    $paymentNominal = Parser::parseLocale($value['payment_nominal_mask']);

                    // skip if payment_nominal == 0
                    if ($paymentNominal == 0) {
                        continue;
                    }

                    $docPath = '';
                    if (isset($value['disburse_docs'])) {
                        $docUrl = FileHelper::upload($value['disburse_docs'], Constants::EXPENSE_DISBURSE_DOC_PATH);
                        if (! $docUrl['status']) {
                            return redirect()->back()->with('error', $docUrl['message'].' '.$value['document_path'])->withInput();
                        }
                        $docPath = $docUrl['url'];
                    }

                    $arrPayments[] = [
                        'expense_id'         => $value['expense_id'],
                        'disburse_docs'      => $docPath,
                        'transaction_number' => $value['transaction_number'],
                        'payment_reference'  => $value['payment_reference'],
                        'payment_method'     => $value['payment_method'],
                        'bank_id'            => $value['bank_id'] ?? null,
                        'payment_at'         => date('Y-m-d', strtotime($value['payment_at'])),
                        'payment_nominal'    => $paymentNominal,
                    ];

                    $expense       = Expense::find($value['expense_id']);
                    $grandTotal    = $expense->grand_total;
                    $totalIsPaid   = $expense->is_paid;
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

                    $expense->update([
                        'payment_status' => array_search($paymentStatus, Constants::EXPENSE_PAYMENT_STATUS),
                    ]);

                    $processedCount += 1;
                }

                ExpenseDisburse::insert($arrPayments);

                return $processedCount;
            });

            $success = ['success' => "Sebanyak {$count} Data Berhasil diupload"];

            return redirect()->route('expense.list.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->route('expense.list.index')->with('error', $e->getMessage())->withInput();
        }
    }

    public function detail(ExpenseDisburse $disburse)
    {
        try {
            $data = $disburse->load(['bank']);

            return $data;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req, ExpenseDisburse $disburse)
    {
        try {
            $data = $disburse->load(['bank']);

            if ($req->isMethod('post')) {
                DB::transaction(function() use ($req, $disburse, $data) {
                    $input = $req->all();

                    $existingDoc = $data->disburse_docs ?? null;
                    $docPath     = '';
                    if ($req->hasFile('document_path')) {
                        if ($existingDoc) {
                            FileHelper::delete($existingDoc);
                        }

                        $docUrl = FileHelper::upload($input['document_path'], constants::EXPENSE_DISBURSE_DOC_PATH);
                        if (! $docUrl['status']) {
                            return redirect()->back()->with('error', $docUrl['message'].' '.$input['document_path'])->withInput();
                        }
                        $docPath = $docUrl['url'];
                    } else {
                        $docPath = $existingDoc;
                    }

                    $disburse->update([
                        'payment_method'     => $input['payment_method'],
                        'bank_id'            => $input['bank_id'],
                        'payment_reference'  => $input['payment_reference'],
                        'transaction_number' => $input['transaction_number'],
                        'payment_nominal'    => Parser::parseLocale($input['payment_nominal']),
                        'payment_at'         => date('Y-m-d', strtotime($input['payment_at'])),
                        'disburse_docs'      => $docPath,
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

    public function delete(ExpenseDisburse $disburse)
    {
        try {
            DB::transaction(function() use ($disburse) {
                $disburse->delete();
            });
            $success = ['success' => 'Data Berhasil dihapus'];

            return redirect()->back()->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        }
    }
}
