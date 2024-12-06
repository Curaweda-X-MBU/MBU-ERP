<?php

namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Ph\PhComplaint;
use App\Models\Project\Project;
use App\Models\Purchase\PurchaseItem;
use App\Models\Inventory\ProductWarehouse;

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
        'created_by'
    ];

    public function uom() {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    public function company() {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function product_category() {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function product_sub_category() {
        return $this->belongsTo(ProductSubCategory::class, 'product_sub_category_id');
    }

    public function ph_complaint() {
        return $this->hasMany(PhComplaint::class, 'product_id');
    }

    public function purchase_item() {
        return $this->hasMany(PurchaseItem::class, 'product_id');
    }

    public function fcr() {
        return $this->hasMany(Fcr::class, 'product_id');
    }

    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    public function project() {
        return $this->hasMany(Project::class, 'kandang_id');
    }

    public function product_warehouse() {
        return $this->hasMany(ProductWarehouse::class, 'product_id');
    }
}
