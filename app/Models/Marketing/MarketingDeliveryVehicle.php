<?php

namespace App\Models\Marketing;

use App\Models\DataMaster\Supplier;
use App\Models\DataMaster\Uom;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingDeliveryVehicle extends Model
{
    use HasFactory;

    protected $table = 'marketing_delivery_vehicles';

    protected $primaryKey = 'marketing_delivery_vehicle_id';

    protected $fillable = [
        'marketing_id',
        'plat_number',
        'marketing_product_id',
        'qty',
        'uom_id',
        'exit_at',
        'sender_id',
        'driver_name',
        'supplier_id',
        'delivery_fee',
    ];

    public function marketing()
    {
        return $this->belongsTo(Marketing::class, 'marketing_id', 'marketing_id');
    }

    public function marketing_product()
    {
        return $this->belongsTo(MarketingProduct::class, 'marketing_product_id', 'marketing_product_id');
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id', 'uom_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'user_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }
}
