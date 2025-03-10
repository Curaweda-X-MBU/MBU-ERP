<?php

namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'locations';

    protected $primaryKey = 'location_id';

    protected $fillable = [
        'name',
        'address',
        'company_id',
        'area_id',
        'created_at',
        'created_by',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'location_id');
    }

    public function kandangs()
    {
        return $this->hasMany(Kandang::class, 'location_id');
    }

    public function warehouse()
    {
        return $this->hasMany(Warehouse::class, 'location_id');
    }
}
