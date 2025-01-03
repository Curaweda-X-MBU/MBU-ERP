<?php

namespace App\Models\Expense;

use App\Models\DataMaster\Kandang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseKandang extends Model
{
    use HasFactory;

    protected $table = 'expense_kandang';

    protected $primaryKey = 'expense_kandang_id';

    protected $fillable = [
        'expense_id',
        'kandang_id',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'expense_id');
    }

    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id');
    }
}
