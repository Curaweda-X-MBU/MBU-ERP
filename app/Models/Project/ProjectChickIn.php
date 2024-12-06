<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DataMaster\Supplier;

class ProjectChickIn extends Model
{
    use HasFactory;
    protected $table = 'project_chickin';
    protected $primaryKey = 'project_chickin_id';

    protected $fillable = [
        'project_chickin_id',
        'project_id',
        'travel_letter_number',
        'travel_letter_document',
        'chickin_date',
        'supplier_id',
        'hatchery',
        'total_chickin'
    ];

    public function project() {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
