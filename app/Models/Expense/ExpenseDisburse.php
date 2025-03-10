<?php

namespace App\Models\Expense;

use App\Constants;
use App\Models\DataMaster\Bank;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExpenseDisburse extends Model
{
    use HasFactory;

    protected $table = 'expense_disburses';

    protected $primaryKey = 'expense_disburse_id';

    protected $fillable = [
        'expense_id',
        'payment_method',
        'bank_id',
        'payment_nominal',
        'payment_reference',
        'transaction_number',
        'payment_at',
        'disburse_docs',
        'notes',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function(ExpenseDisburse $disburse) {
            DB::beginTransaction();
            try {
                $expense = Expense::find($disburse->expense_id);
                $expense->update([
                    'payment_status' => $expense->calculatePaymentStatus(),
                    'expense_status' => array_search('Realisasi', Constants::EXPENSE_STATUS),
                ]);

                $expense->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        });

        static::updated(function(ExpenseDisburse $disburse) {
            if ($disburse->isDirty(['payment_nominal'])) {
                DB::beginTransaction();
                try {
                    $expense = Expense::find($disburse->expense_id);
                    $expense->update([
                        'payment_status' => $expense->calculatePaymentStatus(),
                        'expense_status' => array_search('Realisasi', Constants::EXPENSE_STATUS),
                    ]);
                    $expense->save();

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                }
            }
        });

        static::deleted(function(ExpenseDisburse $disburse) {
            DB::beginTransaction();
            try {
                $expense = Expense::find($disburse->expense_id);
                $expense->update([
                    'payment_status' => $expense->calculatePaymentStatus(),
                ]);
                $expense->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        });
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'bank_id');
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'expense_id');
    }
}
