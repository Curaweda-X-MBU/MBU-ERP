<?php

namespace App\Models\Marketing;

use App\Models\DataMaster\Uom;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingDeliveryVehicle extends Model
{
    use HasFactory;

    protected $table = 'marketing_delivery_vehicle';

    protected $primaryKey = 'marketing_delivery_vehicle_id';

    protected $fillable = [
        'marketing_id',
        'plat_number',
        'qty',
        'uom_id',
        'exit_at',
        'sender_id',
        'driver_name',
    ];

    public function marketing()
    {
        $this->belongsTo(Marketing::class, 'marketing_id', 'marketing_id');
    }

    public function uom()
    {
        $this->belongsTo(Uom::class, 'uom_id', 'uom_id');
    }

    public function sender()
    {
        $this->belongsTo(User::class, 'sender_id', 'user_id');
    }
}
