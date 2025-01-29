<?php

namespace App\Models\Marketing;

use App\Models\DataMaster\Product;
use App\Models\DataMaster\Uom;
use App\Models\DataMaster\Warehouse;
use App\Models\Project\Project;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingProduct extends Model
{
    use HasFactory;

    protected $table = 'marketing_products';

    protected $primaryKey = 'marketing_product_id';

    protected $fillable = [
        'marketing_id',
        'warehouse_id',
        'product_id',
        'project_id',
        'price',
        'weight_avg',
        'uom_id',
        'qty',
        'weight_total',
        'total_price',
        'is_paid',
        'return_qty',
    ];

    public function marketing()
    {
        return $this->belongsTo(Marketing::class, 'marketing_id', 'marketing_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'warehouse_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id', 'uom_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
