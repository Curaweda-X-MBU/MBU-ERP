<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOther extends Model
{
    use HasFactory;

    protected $table = 'purchase_others';

    protected $primaryKey = 'purchase_other_id';

    public $timestamps = false;

    protected $fillable = [
        'purchase_id',
        'name',
        'amount',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }
}
