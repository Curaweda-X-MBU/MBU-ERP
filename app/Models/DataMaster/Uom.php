<?php

namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Purchase\PurchaseItem;

class Uom extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'uom';
    protected $primaryKey = 'uom_id';

    protected $fillable = [
        'name',
        'created_at',
        'created_by'
    ];

    public function product_components() {
        return $this->hasMany(ProductComponent::class, 'uom_id');
    }

    public function products() {
        return $this->hasMany(ProductComponent::class, 'uom_id');
    }

    public function fcr() {
        return $this->hasMany(fcr::class, 'uom_id');
    }
}
