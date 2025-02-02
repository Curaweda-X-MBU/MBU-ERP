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
        'revision_status',
        'document_revision',
        'on_time',
        'day',
        'total_chick',
        'total_depletion',
        'cum_depletion',
        'daily_depletion_rate',
        'cum_depletion_rate',

        'daily_gain',
        'avg_daily_gain',
        'cum_intake',
        'fcr_value',

        'created_by',
    ];

    public function recording_stock()
    {
        return $this->hasMany(RecordingStock::class, 'recording_id');
    }

    public function recording_nonstock()
    {
        return $this->hasMany(RecordingNonstock::class, 'recording_id');
    }

    public function recording_depletion()
    {
        return $this->hasMany(RecordingDepletion::class, 'recording_id');
    }

    public function recording_bw()
    {
        return $this->hasMany(RecordingBw::class, 'recording_id');
    }

    public function recording_egg()
    {
        return $this->hasMany(RecordingEgg::class, 'recording_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
