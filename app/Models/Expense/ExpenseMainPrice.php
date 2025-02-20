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

    protected $appends = ['qty_per_kandang', 'price_per_kandang'];

    public function getQtyPerKandangAttribute()
    {
        $countKandang = max(count($this->expense->expense_kandang), 1);

        return $this->qty / $countKandang;
    }

    public function getPricePerKandangAttribute()
    {
        $countKandang = max(count($this->expense->expense_kandang), 1);

        return $this->price / $countKandang;
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'expense_id');
    }

    public function expense_realizations()
    {
        return $this->hasMany(ExpenseRealization::class, 'expense_item_id', 'expense_item_id');
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
