<?php

namespace App\Models\Expense;

use App\Models\DataMaster\Bank;
use App\Models\UserManagement\User;
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
        'is_approved',
        'approver_id',
        'approved_at',
        'approval_notes',
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

        static::updated(function(ExpenseDisburse $payment) {
            if ($payment->isDirty(['is_approved', 'payment_nominal'])) {
                DB::beginTransaction();
                try {
                    $expense = Expense::find($payment->expense_id);
                    $expense->update([
                        'payment_status' => $expense->calculatePaymentStatus(),
                    ]);
                    $expense->save();

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                }
            }
        });

        static::deleted(function(ExpenseDisburse $payment) {
            DB::beginTransaction();
            try {
                $expense = Expense::find($payment->expense_id);
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

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id', 'user_id');
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
