<?php

namespace App\Models\Purchase;

use App\Models\DataMaster\Bank;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    use HasFactory;

    protected $table = 'purchase_payments';

    protected $primaryKey = 'purchase_payment_id';

    public $timestamps = false;

    protected $fillable = [
        'purchase_id',
        'payment_date',
        'payment_method',
        'own_bank_id',
        'recipient_bank_id',
        'ref_number',
        'transaction_number',
        'bank_charge',
        'amount',
        'document',
        'status',
        'approved_by',
        'reason',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function own_bank()
    {
        return $this->belongsTo(Bank::class, 'own_bank_id', 'bank_id');
    }

    public function recipient_bank()
    {
        return $this->belongsTo(Bank::class, 'recipient_bank_id', 'bank_id');
    }
}
