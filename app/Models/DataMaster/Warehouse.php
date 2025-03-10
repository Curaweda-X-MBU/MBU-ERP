<?php

namespace App\Models\DataMaster;

use App\Models\Inventory\ProductWarehouse;
use App\Models\UserManagement\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'warehouses';

    protected $primaryKey = 'warehouse_id';

    protected $fillable = [
        'name',
        'type',
        'location_id',
        'kandang_id',
        'created_by',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id');
    }

    public function createdby()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function product_warehouse()
    {
        return $this->hasMany(ProductWarehouse::class, 'warehouse_id');
    }
}
