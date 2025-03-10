<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordingBw extends Model
{
    use HasFactory;

    protected $table = 'recording_bw';

    protected $primaryKey = 'recording_bw_id';

    public $timestamps = false;

    protected $fillable = [
        'recording_id',
        'avg_weight',
        'total_chick',
        'total_calc',
        'value',
        'notes',
    ];

    public function recording()
    {
        return $this->belongsTo(Recording::class, 'recording_id');
    }

    public function recordingBwList()
    {
        return $this->hasMany(RecordingBwList::class, 'recording_bw_id');
    }
}
