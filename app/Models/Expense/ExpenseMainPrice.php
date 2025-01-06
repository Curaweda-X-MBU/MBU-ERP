<?php

namespace App\Models\Expense;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseMainPrice extends Model
{
    use HasFactory;

    protected $table = 'expense_main_prices';

    protected $primaryKey = 'expense_main_price_id';

    protected $fillable = [
        'expense_id',
        'sub_category',
        'qty',
        'uom',
        'price',
        'notes',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'expense_id');
    }
}
