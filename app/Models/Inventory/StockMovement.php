<?php

namespace App\Models\Inventory;

use App\Models\DataMaster\Product;
use App\Models\DataMaster\Warehouse;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'stock_movements';

    protected $primaryKey = 'stock_movement_id';

    protected $fillable = [
        'origin_id',
        'destination_id',
        'product_id',
        'transfer_qty',
        'notes',
        'created_by',
    ];

    public function createdby()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get the origin that owns the StockMovement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function origin()
    {
        return $this->belongsTo(Warehouse::class, 'origin_id', 'warehouse_id');
    }

    /**
     * Get the destination that owns the StockMovement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function destination()
    {
        return $this->belongsTo(Warehouse::class, 'destination_id', 'warehouse_id');
    }

    /**
     * Get the product that owns the StockMovement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get all of the stock_movement_vehicle for the StockMovement
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stock_movement_vehicle()
    {
        return $this->hasMany(StockMovementVehicle::class, 'stock_movement_id');
    }
}
