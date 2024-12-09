<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Recording extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'recordings';
    protected $primaryKey = 'recording_id';

    protected $fillable = [
        'project_id',
        'record_datetime',
        'status',
        'on_time',
        'created_by'
    ];

    /**
     * Get all of the comments for the Recording
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recording_stock() {
        return $this->hasMany(RecordingStock::class, 'recording_id');
    }

    /**
     * Get all of the recording_depletion for the Recording
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recording_depletion() {
        return $this->hasMany(RecordingDepletion::class, 'recording_id');
    }

}
