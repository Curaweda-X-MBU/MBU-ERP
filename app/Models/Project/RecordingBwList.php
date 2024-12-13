<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordingBwList extends Model
{
    use HasFactory;

    protected $table = 'recording_bw_lists';

    protected $primaryKey = 'recording_bw_list_id';

    public $timestamps = false;

    protected $fillable = [
        'recording_bw_id',
        'weight',
        'total',
        'weight_calc',
    ];

    /**
     * Get the recordingBw that owns the RecordingBwList
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recordingBw()
    {
        return $this->belongsTo(RecordingBw::class, 'recording_bw_id');
    }
}
