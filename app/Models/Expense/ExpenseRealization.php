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

    protected $appends = ['qty_per_kandang', 'price_per_kandang'];

    public function getQtyPerKandangAttribute()
    {
        $countKandang = max(count($this->expense->expense_kandang), 1);

        return ($this->qty ?? 0) / $countKandang;
    }

    public function getPricePerKandangAttribute()
    {
        $countKandang = max(count($this->expense->expense_kandang), 1);

        return ($this->price ?? 0) / $countKandang;
    }

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
