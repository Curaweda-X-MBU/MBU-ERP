<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectRecording extends Model
{
    use HasFactory;
    protected $table = 'project_recordings';
    protected $primaryKey = 'project_recording_id';

    protected $fillable = [
        'item',
        'unit_name',
        'interval',
        'project_id'
    ];

    public function project() {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
