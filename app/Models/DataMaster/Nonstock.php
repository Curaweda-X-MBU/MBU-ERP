<?php

namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Project\RecordingNonstock;

class Nonstock extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'nonstocks';
    protected $primaryKey = 'nonstock_id';

    protected $fillable = [
        'name',
        'uom_id',
        'created_at',
        'created_by'
    ];

    /**
     * Get the user that owns the Nonstock
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uom() {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    /**
     * Get all of the recording_nonstock for the Nonstock
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recording_nonstock() {
        return $this->hasMany(RecordingNonstock::class, 'nonstock_id');
    }
    
}
