<?php

namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductComponent extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'product_components';
    protected $primaryKey = 'product_component_id';

    protected $fillable = [
        'name',
        'supplier_id',
        'brand',
        'uom_id',
        'price',
        'tax',
        'expiry_period',
        'created_by'
    ];

    public function supplier() {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function uom() {
        return $this->belongsTo(Uom::class, 'uom_id');
    }
}
