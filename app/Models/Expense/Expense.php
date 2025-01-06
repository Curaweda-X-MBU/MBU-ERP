<?php

namespace App\Models\Expense;

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
        'id_expense',
        'is_approved',
        'approver_id',
        'approval_notes',
        'approved_at',
        'location_id',
        'category',
        'payment_status',
        'expense_status',
        'created_by',
    ];

    protected $append = [
        'grand_total',
    ];

    public function getGrandTotalAttribute()
    {
        return $this->expense_main_prices->sum('price') + $this->expense_addit_prices->sum('price');
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

    public function expense_payments()
    {
        return $this->hasMany(ExpensePayment::class, 'expense_id', 'expense_id');
    }
}
