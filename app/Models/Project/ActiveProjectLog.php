<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveProjectLog extends Model
{
    use HasFactory;

    protected $table = 'active_projects_log';

    protected $primaryKey = 'active_projects_log_id';

    protected $fillable = [
        'period',
        'project_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
