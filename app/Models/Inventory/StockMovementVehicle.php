<?php

namespace App\Models\Inventory;

use App\Models\DataMaster\Supplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovementVehicle extends Model
{
    use HasFactory;

    protected $table = 'stock_movement_vehicles';

    protected $primaryKey = 'stock_movement_vehicle_id';

    public $timestamps = false;

    protected $fillable = [
        'stock_movement_id',
        'supplier_id',
        'vehicle_number',
        'travel_document_number',
        'travel_document',
        'transport_amount_item',
        'transport_amount',
        'driver_name',
    ];

    /**
     * Get the stock_movement that owns the StockMovementVehicle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock_movement()
    {
        return $this->belongsTo(StockMovement::class, 'stock_movement_id');
    }

    /**
     * Get the supplier that owns the StockMovementVehicle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
