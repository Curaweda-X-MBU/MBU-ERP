<?php

namespace App\Models\Expense;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseAdditPrice extends Model
{
    use HasFactory;

    protected $table = 'expense_addit_prices';

    protected $primaryKey = 'expense_addit_price_id';

    protected $fillable = [
        'expense_id',
        'name',
        'price',
        'notes',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'expense_id');
    }
}
