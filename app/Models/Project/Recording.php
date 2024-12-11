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
