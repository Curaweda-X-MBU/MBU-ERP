<?php

namespace App\Models\Purchase;

use App\Models\DataMaster\Supplier;
use App\Models\DataMaster\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItemReception extends Model
{
    use HasFactory;

    protected $table = 'purchase_item_receptions';

    protected $primaryKey = 'purchase_item_reception_id';

    public $timestamps = false;

    protected $fillable = [
        'purchase_item_id',
        'warehouse_id',
        'received_date',
        'travel_number',
        'travel_number_document',
        'vehicle_number',
        'total_received',
        'total_retur',
        'supplier_id',
        'transport_per_item',
        'transport_total',
    ];

    public function purchase_item()
    {
        return $this->belongsTo(PurchaseItem::class, 'purchase_item_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
