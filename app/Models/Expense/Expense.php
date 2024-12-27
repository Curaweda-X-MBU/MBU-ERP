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
        'is_approved',
        'approver_id',
        'approval_notes',
        'approved_at',
        'location_id',
        'category',
        'grand_total',
        'expense_status',
        'created_by',
    ];

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

    public function expense_items()
    {
        return $this->hasMany(ExpenseKandang::class, 'expense_id', 'expense_id');
    }
}
