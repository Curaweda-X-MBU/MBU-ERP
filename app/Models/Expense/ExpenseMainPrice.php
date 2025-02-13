<?php

namespace App\Models\Expense;

use App\Models\DataMaster\Nonstock;
use App\Models\DataMaster\Supplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseMainPrice extends Model
{
    use HasFactory;

    protected $table = 'expense_main_prices';

    protected $primaryKey = 'expense_item_id';

    protected $fillable = [
        'expense_id',
        'nonstock_id',
        'supplier_id',
        'qty',
        'price',
        'notes',
    ];

    protected $appends = ['total_qty', 'total_price'];

    public function getTotalQtyAttribute()
    {
        $countKandang = count($this->expense->expense_kandang) ?: 1;

        return $this->qty / $countKandang;
    }

    public function getTotalPriceAttribute()
    {
        $countKandang = count($this->expense->expense_kandang) ?: 1;

        return $this->price / $countKandang;
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'expense_id');
    }

    public function nonstock()
    {
        return $this->belongsTo(Nonstock::class, 'nonstock_id', 'nonstock_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }
}
