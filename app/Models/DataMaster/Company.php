<?php

namespace App\Models\DataMaster;

use App\Models\UserManagement\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'companies';

    protected $primaryKey = 'company_id';

    protected $fillable = [
        'name',
        'alias',
        'address',
        'created_at',
        'created_by',
    ];

    public function departments()
    {
        return $this->hasMany(Department::class, 'company_id');
    }

    public function roles()
    {
        return $this->hasMany(Role::class, 'company_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'company_id');
    }

    public function kandangs()
    {
        return $this->hasMany(Kandang::class, 'company_id');
    }

    public function locations()
    {
        return $this->hasMany(Location::class, 'company_id');
    }
}
