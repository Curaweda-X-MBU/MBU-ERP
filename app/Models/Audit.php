<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DataMaster\Department;

class Audit extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'audits';
    protected $primaryKey = 'audit_id';

    protected $fillable = [
        'title',
        'category',
        'priority',
        'document',
        'description',
        'department_id',
        'created_at',
        'created_by'
    ];

    public function department() {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
