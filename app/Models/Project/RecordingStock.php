<?php

namespace App\Models\Project;

use App\Models\Inventory\ProductWarehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordingStock extends Model
{
    use HasFactory;

    protected $table = 'recording_stocks';

    protected $primaryKey = 'recording_stock_id';

    public $timestamps = false;

    protected $fillable = [
        'recording_id',
        'product_warehouse_id',
        'increase',
        'decrease',
        'usage_amount',
        'notes',
    ];

    /**
     * Get the recording that owns the RecordingStock
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recording()
    {
        return $this->belongsTo(Recording::class, 'recording_id');
    }

    /**
     * Get the product_warehouse that owns the RecordingStock
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product_warehouse()
    {
        return $this->belongsTo(ProductWarehouse::class, 'product_warehouse_id');
    }
}
