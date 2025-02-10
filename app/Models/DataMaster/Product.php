<?php

namespace App\Models\DataMaster;

use App\Models\Inventory\ProductWarehouse;
use App\Models\Marketing\MarketingProduct;
use App\Models\Ph\PhComplaint;
use App\Models\Purchase\PurchaseItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'products';

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'brand',
        'uom_id',
        'sku',
        'company_id',
        'product_category_id',
        'product_sub_category_id',
        'product_price',
        'selling_price',
        'tax',
        'expiry_period',
        'can_be_sold',
        'can_be_purchased',
        'is_active',
        'created_by',
    ];

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function product_sub_category()
    {
        return $this->belongsTo(ProductSubCategory::class, 'product_sub_category_id');
    }

    public function ph_complaint()
    {
        return $this->hasMany(PhComplaint::class, 'product_id');
    }

    public function purchase_item()
    {
        return $this->hasMany(PurchaseItem::class, 'product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function product_warehouse()
    {
        return $this->hasMany(ProductWarehouse::class, 'product_id');
    }

    public function marketing_product()
    {
        return $this->hasMany(MarketingProduct::class, 'product_id', 'product_id');
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier', 'product_id', 'supplier_id')
            ->withPivot(['product_price', 'selling_price'])
            ->withTimestamps();
    }
}
