<?php

namespace App\Models\Marketing;

use App\Constants;
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

    protected $appends = ['grand_total', 'payment_status'];

    public function getGrandTotalAttribute()
    {
        $marketing = $this->marketing;
        if (! $marketing) {
            throw new \Exception("Marketing not found for product ID {$this->marketing_product_id}");
        }

        $discount             = $marketing->discount;
        $additionalPrices     = $marketing->marketing_addit_prices->sum('price');
        $productCount         = $marketing->marketing_products()->count();
        $localDiscount        = $discount         / $productCount;
        $localAdditionalPrice = $additionalPrices / $productCount;
        $tax                  = $marketing->tax;

        return $this->total_price + ($this->total_price * ($tax / 100)) - $localDiscount + $localAdditionalPrice;
    }

    public function getPaymentStatusAttribute()
    {
        $isPaid = $this->is_paid ?? 0;
        if ($this->grand_total < $isPaid) {
            return array_search('Dibayar Penuh', Constants::MARKETING_PAYMENT_STATUS);
        } elseif ($this->grand_total == $isPaid) {
            return array_search('Dibayar Penuh', Constants::MARKETING_PAYMENT_STATUS);
        } elseif ($this->grand_total > $isPaid && $isPaid > 0) {
            return array_search('Dibayar Sebagian', Constants::MARKETING_PAYMENT_STATUS);
        } else {
            return array_search('Tempo', Constants::MARKETING_PAYMENT_STATUS);
        }
    }

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
