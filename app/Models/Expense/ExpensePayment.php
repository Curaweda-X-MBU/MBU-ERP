<?php

namespace App\Models\Expense;

use App\Models\DataMaster\Bank;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpensePayment extends Model
{
    use HasFactory;

    protected $table = 'expense_payments';

    protected $primaryKey = 'expense_payment_id';

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
        'document_path',
        'notes',
        'verify_status',
    ];

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
