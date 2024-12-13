<?php

namespace App\Models\Marketing;

use App\Models\DataMaster\Kandang;
use App\Models\DataMaster\Product;
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
        'qty',
        'weight_total',
        'total_price',
    ];

    public function marketing()
    {
        $this->belongsTo(Marketing::class, 'marketing_id', 'marketing_id');
    }

    public function kandang()
    {
        $this->belongsTo(Kandang::class, 'kandang_id', 'kandang_id');
    }

    public function product()
    {
        $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
