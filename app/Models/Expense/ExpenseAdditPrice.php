<?php

namespace App\Models\Expense;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseAdditPrice extends Model
{
    use HasFactory;

    protected $table = 'expense_items';

    protected $primaryKey = 'expense_item_id';

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
