<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectPhase extends Model
{
    use HasFactory;
    protected $table = 'project_phases';
    protected $primaryKey = 'project_phase_id';

    protected $fillable = [
        'name',
        'start_date_estimate',
        'end_date_estimate',
        'project_id'
    ];

    public function project() {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
