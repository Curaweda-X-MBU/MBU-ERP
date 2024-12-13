<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\DataMaster\Company;

class Role extends SpatieRole
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'roles';
    protected $primaryKey = 'role_id';

    protected $fillable = [
        'name',
        'company_id',
        'guard_name',
        'all_area',
        'all_location',
        'company_id',
        'created_at',
        'created_by'
    ];

    public function company() {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(config('auth.providers.users.model'));
    }
}
