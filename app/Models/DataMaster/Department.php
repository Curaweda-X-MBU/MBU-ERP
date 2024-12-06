<?php

namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserManagement\User;
use App\Models\Audit;

class Department extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'departments';
    protected $primaryKey = 'department_id';

    protected $fillable = [
        'name',
        'company_id',
        'location_id',
        'created_at',
        'created_by'
    ];

    public function company() {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function location() {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function audits() {
        return $this->hasMany(Audit::class, 'department_id');
    }

    public function users() {
        return $this->hasMany(User::class, 'department_id');
    }
}
