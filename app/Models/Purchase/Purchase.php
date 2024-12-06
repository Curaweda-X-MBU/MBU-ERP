<?php

namespace App\Models\Purchase;

use App\Models\DataMaster\Supplier;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'purchases';

    protected $primaryKey = 'purchase_id';

    protected $fillable = [
        'pr_number',
        'po_number',
        'po_date',
        'supplier_id',
        'require_date',
        'total_before_tax',
        'total_after_tax',
        'total_tax',
        'total_discount',
        'total_other_amount',
        'total_received',
        'total_not_received',
        'total_retur',
        'total_payment',
        'total_remaining_payment',
        'grand_total',
        'notes',
        'status',
        'rejected',
        'approval_line',
        'total_amount_received',
        'total_amount_not_received',
        'total_amount_retur',
        'created_by',
    ];

    public function createdby()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function purchase_item()
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id');
    }

    public function purchase_other()
    {
        return $this->hasMany(PurchaseOther::class, 'purchase_id');
    }

    public function purchase_payment()
    {
        return $this->hasMany(PurchasePayment::class, 'purchase_id');
    }
}
