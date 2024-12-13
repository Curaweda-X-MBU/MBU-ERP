<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DataMaster\Product;
use App\Models\Project\Project;
use App\Models\DataMaster\Warehouse;
use App\Models\DataMaster\Uom;

class PurchaseItemReception extends Model
{
    use HasFactory;
    protected $table = 'purchase_item_receptions';
    protected $primaryKey = 'purchase_item_reception_id';
    public $timestamps = false;

    protected $fillable = [
        'purchase_item_id',
        'received_date',
        'travel_number',
        'travel_number_document',
        'vehicle_number',
        'total_received',
        'total_retur'
    ];

    public function purchase_item() {
        return $this->belongsTo(PurchaseItem::class, 'purchase_item_id');
    }
}
