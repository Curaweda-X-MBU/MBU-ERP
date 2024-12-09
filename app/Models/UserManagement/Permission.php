<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory;

    protected $table = 'permissions';

    protected $primaryKey = 'permission_id';

    protected $fillable = [
        'name',
        'guard_name',
        'created_at',
        'updated_at',
    ];
}
