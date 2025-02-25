<?php

namespace App\Models\Expense;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseRealization extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'expense_realizations';

    protected $primaryKey = 'expense_realization_id';

    protected $fillable = [
        'expense_id',
        'expense_item_id',
        'expense_addit_price_id',
        'qty',
        'price',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id');
    }

    public function expense_main_price()
    {
        return $this->belongsTo(ExpenseMainPrice::class, 'expense_item_id');
    }

    public function expense_addit_price()
    {
        return $this->belongsTo(ExpenseAdditPrice::class, 'expense_addit_price_id');
    }
}
