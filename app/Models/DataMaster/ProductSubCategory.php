<?php

namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSubCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'product_sub_categories';

    protected $primaryKey = 'product_sub_category_id';

    protected $fillable = [
        'name',
        'product_category_id',
        'created_at',
        'created_by',
    ];

    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'product_sub_category_id');
    }
}
