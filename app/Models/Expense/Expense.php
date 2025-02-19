<?php

namespace App\Models\Expense;

use App\Constants;
use App\Models\DataMaster\Location;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'expenses';

    protected $primaryKey = 'expense_id';

    protected $fillable = [
        'parent_expense_id',
        'id_expense',
        'po_number',
        'transaction_date',
        'is_approved',
        'approver_id',
        'approval_notes',
        'approved_at',
        'location_id',
        'category',
        'bill_docs',
        'realization_docs',
        'payment_status',
        'expense_status',
        'created_by',
    ];

    protected $appends = [
        'grand_total',
        'is_paid',
        'total_qty',
        'not_paid',
    ];

    public function getGrandTotalAttribute()
    {
        return $this->expense_main_prices->sum('price') + $this->expense_addit_prices->sum('price');
    }

    public function getIsPaidAttribute()
    {
        return $this->expense_payments->where('verify_status', 2)->sum('payment_nominal');
    }

    public function getTotalQtyAttribute()
    {
        return $this->expense_main_prices->sum('total_qty');
    }

    public function getNotPaidAttribute()
    {
        return $this->grand_total - $this->is_paid;
    }

    public function calculatePaymentStatus()
    {
        $grandTotal    = $this->grand_total;
        $totalPayments = $this->is_paid;

        if ($grandTotal < $totalPayments) {
            return array_search('Dibayar Lebih', Constants::MARKETING_PAYMENT_STATUS);
        } elseif ($grandTotal == $totalPayments) {
            return array_search('Dibayar Penuh', Constants::MARKETING_PAYMENT_STATUS);
        } elseif ($grandTotal > $totalPayments && $totalPayments > 0) {
            return array_search('Dibayar Sebagian', Constants::MARKETING_PAYMENT_STATUS);
        } else {
            return array_search('Tempo', Constants::MARKETING_PAYMENT_STATUS);
        }
    }

    public function created_user()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id', 'user_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    public function expense_kandang()
    {
        return $this->hasMany(ExpenseKandang::class, 'expense_id', 'expense_id');
    }

    public function expense_main_prices()
    {
        return $this->hasMany(ExpenseMainPrice::class, 'expense_id', 'expense_id');
    }

    public function expense_addit_prices()
    {
        return $this->hasMany(ExpenseAdditPrice::class, 'expense_id', 'expense_id');
    }

    public function expense_disburse()
    {
        return $this->hasMany(ExpenseDisburse::class, 'expense_id', 'expense_id');
    }

    public function parent_expense()
    {
        return $this->belongsTo(Expense::class, 'parent_expense_id', 'expense_id');
    }

    public function child_expense()
    {
        return $this->hasOne(Expense::class, 'parent_expense_id', 'expense_id');
    }
}
