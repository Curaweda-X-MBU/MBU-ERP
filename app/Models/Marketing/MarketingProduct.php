<?php

namespace App\Models\Marketing;

use App\Models\DataMaster\Kandang;
use App\Models\DataMaster\Product;
use App\Models\DataMaster\Uom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingProduct extends Model
{
    use HasFactory;

    protected $table = 'marketing_products';

    protected $primaryKey = 'marketing_product_id';

    protected $fillable = [
        'marketing_id',
        'kandang_id',
        'product_id',
        'price',
        'weight_avg',
        'uom_id',
        'qty',
        'weight_total',
        'total_price',
    ];

    public function marketing()
    {
        return $this->belongsTo(Marketing::class, 'marketing_id', 'marketing_id');
    }

    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id', 'kandang_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id', 'uom_id');
    }
}
