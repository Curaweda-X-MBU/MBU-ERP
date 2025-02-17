<?php

namespace App\Models\Purchase;

use App\Models\DataMaster\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItemAlocation extends Model
{
    use HasFactory;

    protected $table = 'purchase_item_alocations';

    protected $primaryKey = 'purchase_item_alocation_id';

    public $timestamps = false;

    protected $fillable = [
        'purchase_item_id',
        'warehouse_id',
        'alocation_qty',
    ];

    public function purchase_item()
    {
        return $this->belongsTo(PurchaseItem::class, 'purchase_item_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
