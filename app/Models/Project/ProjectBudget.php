<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectBudget extends Model
{
    use HasFactory;
    protected $table = 'project_budgets';
    protected $primaryKey = 'project_budget_id';

    protected $fillable = [
        'item',
        'qty',
        'price',
        'total',
        'project_id'
    ];

    public function project() {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
