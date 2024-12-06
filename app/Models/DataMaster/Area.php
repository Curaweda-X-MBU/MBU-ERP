<?php

namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'areas';
    protected $primaryKey = 'area_id';

    protected $fillable = [
        'name',
        'created_at',
        'created_by'
    ];

    public function locations() {
        return $this->hasMany(Location::class, 'area_id');
    }
}
