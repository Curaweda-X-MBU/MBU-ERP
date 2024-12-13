<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DataMaster\Nonstock;

class RecordingNonstock extends Model
{
    use HasFactory;
    protected $table = 'recording_nonstocks';
    protected $primaryKey = 'recording_nonstock_id';

    public $timestamps = false;
    protected $fillable = [
        'recording_id',
        'nonstock_id',
        'value',
        'notes'
    ];

    /**
     * Get the nonstock that owns the RecordingNonstock
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nonstock() {
        return $this->belongsTo(Nonstock::class, 'nonstock_id');
    }

    /**
     * The recording that belong to the RecordingNonstock
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function recording() {
        return $this->belongsTo(Recording::class, 'recording_id');
    }
}
