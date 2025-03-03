<?php

namespace App\Models\Expense;

use App\Models\DataMaster\Bank;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseReturnPayment extends Model
{
    use HasFactory;

    protected $table = 'expense_returns';

    protected $primaryKey = 'expense_return_id';

    protected $fillable = [
        'expense_id',
        'payment_method',
        'bank_id',
        'bank_recipient_id',
        'payment_reference',
        'transaction_number',
        'payment_nominal',
        'bank_admin_fees',
        'payment_at',
        'return_docs',
        'notes',
        'created_by',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'expense_id');
    }

    public function created_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'bank_id');
    }

    public function recipient_bank()
    {
        return $this->belongsTo(Bank::class, 'bank_recipient_id', 'bank_id');
    }
}
