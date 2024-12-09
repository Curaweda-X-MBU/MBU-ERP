<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\ProductWarehouse;

class RecordingDepletion extends Model
{
    use HasFactory;
    protected $table = 'recording_depletions';
    protected $primaryKey = 'recording_depletion_id';

    protected $fillable = [
        'recording_id',
        'product_warehouse_id',
        'total',
        'notes'
    ];

    /**
     * Get the recording that owns the RecordingDepletion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recording() {
        return $this->belongsTo(Recording::class, 'recording_id');
    }

    public function product_warehouse() {
        return $this->belongsTo(ProductWarehouse::class, 'product_warehouse_id');
    }
}
