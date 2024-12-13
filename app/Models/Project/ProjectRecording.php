<?php

namespace App\Models\Project;

use App\Models\DataMaster\Uom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectRecording extends Model
{
    use HasFactory;

    protected $table = 'project_recordings';

    protected $primaryKey = 'project_recording_id';

    protected $fillable = [
        'item',
        'uom_id',
        'interval',
        'project_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }
}
