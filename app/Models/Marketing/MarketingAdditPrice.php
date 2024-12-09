<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingAdditPrice extends Model
{
    use HasFactory;

    protected $table = 'marketing_addit_prices';

    protected $primaryKey = 'marketing_addit_price_id';

    protected $fillable = [
        'item',
        'price',
        'marketing_id',
    ];

    public function marketing()
    {
        return $this->belongsTo(Marketing::class, 'marketing_id', 'marketing_id');
    }
}
