<?php

namespace App\Models\Expense;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseMainPrice extends Model
{
    use HasFactory;

    protected $table = 'expense_main_prices';

    protected $primaryKey = 'expense_item_id';

    protected $fillable = [
        'expense_id',
        'sub_category',
        'qty',
        'uom',
        'price',
        'notes',
    ];

    protected $appends = ['total_qty', 'total_price'];

    public function getTotalQtyAttribute()
    {
        $countKandang = count($this->expense->expense_kandang) ?: 1;

        return $this->qty * $countKandang;
    }

    public function getTotalPriceAttribute()
    {
        $countKandang = count($this->expense->expense_kandang) ?: 1;

        return $this->price * $countKandang;
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'expense_id');
    }
}
