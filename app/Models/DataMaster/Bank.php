<?php

namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'banks';
    protected $primaryKey = 'bank_id';

    protected $fillable = [
        'name',
        'owner',
        'account_number',
        'alias',
        'created_at',
        'created_by'
    ];

    public function purchase_payment_own() {
        return $this->hasMany(PurchaseItem::class, 'own_bank_id', 'bank_id');
    }

    public function purchase_payment_recipient() {
        return $this->hasMany(PurchaseItem::class, 'recipient_bank_id', 'bank_id');
    }
}
