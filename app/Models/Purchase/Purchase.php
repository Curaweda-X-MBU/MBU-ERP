<?php

namespace App\Models\Purchase;

use App\Models\DataMaster\Supplier;
use App\Models\DataMaster\Warehouse;
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

    protected $casts = [
        'warehouse_ids' => 'array',
    ];

    protected $fillable = [
        'pr_number',
        'po_number',
        'po_date',
        'supplier_id',
        'warehouse_ids',
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

    public function getWarehouseDetailsAttribute()
    {
        return Warehouse::whereIn('warehouse_id', $this->warehouse_ids)
            ->with(['location', 'location.area', 'location.company'])
            ->get()
            ->map(function($warehouse) {
                return [
                    'warehouse_name' => $warehouse->name,
                    'location_name'  => $warehouse->location->name          ?? 'N/A',
                    'area_name'      => $warehouse->location->area->name    ?? 'N/A',
                    'company_name'   => $warehouse->location->company->name ?? 'N/A',
                ];
            })
            ->toArray();
    }
}
